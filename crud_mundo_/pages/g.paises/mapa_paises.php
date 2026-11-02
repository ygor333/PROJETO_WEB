<?php
/**
 * mapa_paises.php  (arquivo único)
 *
 * - Sem parâmetros ........ exibe a página do Mapa-Múndi.
 * - ?api=1 ................ JSON com os países do banco + dados da REST Countries.
 * - ?api=buscar&q=Japão ... JSON com um país pesquisado na REST Countries.
 *
 * Fluxo: SELECT em `paises` -> requisição HTTP (cURL) à REST Countries v5
 * -> json_decode da resposta -> json_encode para o JavaScript do mapa.
 * A chave da API fica só no PHP; o navegador nunca a vê.
 */
require_once __DIR__ . '/../login/verificar_sessao.php';
require_once __DIR__ . '/../../config/google_maps.php';
require_once __DIR__ . '/../../config/restcountries.php';

const RC_BASE   = 'https://api.restcountries.com/countries/v5';
const RC_CAMPOS = 'names.common,names.translations,capitals,coordinates,flag.url_png,region,population,codes.alpha_2';

$erroApi     = '';    // último erro de comunicação com a API (aparece na tela)
$apiBloqueada = false; // true se a API recusou a chave/limite (não adianta insistir)

/** Chave configurada em config/restcountries.php (a chave demo não serve). */
function restCountriesConfigurada()
{
    global $restCountriesKey;
    $k = isset($restCountriesKey) ? trim($restCountriesKey) : '';
    return $k !== '' && $k !== 'SUA_CHAVE_API' && $k !== 'rc_live_demo';
}

/** Verdadeiro quando o sistema roda no computador local (XAMPP/WAMP/Laragon). */
function emLocalhost()
{
    $host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
    return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
}

/** Pasta do cache: ./cache (criada se possível); senão, a pasta temporária. */
function pastaCache()
{
    $pasta = __DIR__ . '/cache';
    if (!is_dir($pasta)) {
        @mkdir($pasta, 0775, true);
    }
    return (is_dir($pasta) && is_writable($pasta)) ? $pasta : sys_get_temp_dir();
}

/** Requisição GET com cURL; devolve o JSON decodificado ou null. */
function requisitarJson($url)
{
    global $erroApi, $apiBloqueada, $restCountriesKey;

    // 1ª tentativa verificando o certificado SSL; 2ª (só em localhost) sem verificar,
    // pois o PHP do XAMPP costuma vir sem o cacert.pem.
    foreach ([true, false] as $verificarSsl) {
        if (!$verificarSsl && !emLocalhost()) {
            break;
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // devolve o corpo como string
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'CRUD-Mundo/1.0 (projeto escolar)');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . trim($restCountriesKey),
        ]);
        if (!$verificarSsl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $resposta = curl_exec($curl);
        $status   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $erroCurl = curl_error($curl);
        curl_close($curl);

        if ($resposta === false) {
            $erroApi = 'cURL: ' . $erroCurl;
            continue;
        }

        $dados = json_decode($resposta, true);

        if ($status !== 200) {
            $msg = isset($dados['errors'][0]['message'])
                ? $dados['errors'][0]['message']
                : substr(trim(strip_tags($resposta)), 0, 150);
            $erroApi = 'HTTP ' . $status . ': ' . $msg;
            if (in_array($status, [401, 403, 410, 429], true)) {
                $apiBloqueada = true; // chave inválida, conta congelada ou limite de taxa
            }
            return null;
        }
        return is_array($dados) ? $dados : null;
    }
    return null;
}

/** Todos os nomes do país na resposta (inglês + traduções). */
function nomesDoPais($obj)
{
    $nomes = [];
    if (!empty($obj['names']['common'])) {
        $nomes[] = $obj['names']['common'];
    }
    if (!empty($obj['names']['translations']) && is_array($obj['names']['translations'])) {
        foreach ($obj['names']['translations'] as $t) {
            if (is_array($t) && !empty($t['common'])) {
                $nomes[] = $t['common'];
            }
        }
    }
    return $nomes;
}

/** Entre vários resultados, prefere o que tem nome exato igual ao procurado. */
function escolherPais($lista, $nome)
{
    $alvo = mb_strtolower(trim($nome));
    foreach ($lista as $obj) {
        foreach (nomesDoPais($obj) as $n) {
            if (mb_strtolower($n) === $alvo) {
                return $obj;
            }
        }
    }
    return $lista[0];
}

/** Converte o formato da v5 (aninhado) para o formato simples usado no mapa. */
function normalizarPais($obj)
{
    if (!isset($obj['coordinates']['lat']) || !isset($obj['coordinates']['lng'])) {
        return null;
    }
    $nomePt = isset($obj['names']['translations']['por']['common'])
        ? $obj['names']['translations']['por']['common']
        : (isset($obj['names']['common']) ? $obj['names']['common'] : '');

    return [
        'nome_api'  => $nomePt,
        'capital'   => isset($obj['capitals'][0]['name']) ? $obj['capitals'][0]['name'] : '',
        'lat'       => (float) $obj['coordinates']['lat'],
        'lng'       => (float) $obj['coordinates']['lng'],
        'bandeira'  => isset($obj['flag']['url_png']) ? $obj['flag']['url_png'] : '',
        'populacao' => (isset($obj['population']) && is_numeric($obj['population'])) ? (int) $obj['population'] : null,
        'regiao'    => isset($obj['region']) ? $obj['region'] : '',
        'codigo'    => isset($obj['codes']['alpha_2']) ? $obj['codes']['alpha_2'] : '',
    ];
}

/** Procura o país na REST Countries v5 (traduções e, depois, nomes em inglês). */
function buscarPais($nome)
{
    global $erroApi, $apiBloqueada;
    static $chamadas = 0;

    // Cache de 2 dias: poupa a cota do plano grátis (1.000 consultas/mês)
    $cache = pastaCache() . '/rc5_' . md5(mb_strtolower($nome)) . '.json';
    if (is_file($cache) && time() - filemtime($cache) < 172800) {
        $guardado = json_decode(file_get_contents($cache), true);
        if (is_array($guardado)) {
            return $guardado;
        }
    }
    if ($apiBloqueada) {
        return null;
    }

    $q       = rawurlencode($nome);
    $sufixo  = '&limit=25&response_fields=' . RC_CAMPOS;
    $urls    = [
        RC_BASE . '/names.translations?q=' . $q . $sufixo, // nome em português (ou outro idioma)
        RC_BASE . '/name?q=' . $q . $sufixo,               // nomes em inglês / oficiais
    ];

    foreach ($urls as $url) {
        // Limite da API: 20 requisições a cada 10 s -> espaça se houver muitas
        if (++$chamadas > 15) {
            usleep(500000);
        }

        $resposta = requisitarJson($url);
        if ($resposta === null) {
            if ($apiBloqueada) {
                return null;
            }
            continue;
        }

        $lista = (isset($resposta['data']['objects']) && is_array($resposta['data']['objects']))
            ? $resposta['data']['objects'] : [];
        if (empty($lista)) {
            $erroApi = 'Nenhum resultado para "' . $nome . '"';
            continue;
        }

        $pais = normalizarPais(escolherPais($lista, $nome));
        if ($pais === null) {
            $erroApi = 'País sem coordenadas na resposta (' . $nome . ')';
            continue;
        }

        @file_put_contents($cache, json_encode($pais)); // se não gravar, segue sem cache
        return $pais;
    }
    return null;
}

/** Limpa qualquer saída pendente e responde em JSON. */
function responderJson($dados, $codigo = 200)
{
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($codigo);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

// ===================== MODO API (?api=...) =====================
if (isset($_GET['api'])) {
    ob_start();                      // segura qualquer saída acidental
    ini_set('display_errors', '0');  // erros não saem como HTML no meio do JSON

    $avisosPhp = [];
    set_error_handler(function ($n, $msg, $arq, $linha) use (&$avisosPhp) {
        $avisosPhp[] = $msg . ' (linha ' . $linha . ')';
        return true;
    });
    set_exception_handler(function ($e) {
        responderJson(['erro' => 'Erro no servidor: ' . $e->getMessage()], 500);
    });

    if (!restCountriesConfigurada()) {
        responderJson(['erro' => 'Chave da REST Countries não configurada. Informe a chave em config/restcountries.php.'], 400);
    }

    // ---- Pesquisa de um país (caixa de busca do mapa) ----
    if ($_GET['api'] === 'buscar') {
        $nome = isset($_GET['q']) ? trim($_GET['q']) : '';
        if ($nome === '') {
            responderJson(['erro' => 'Informe o nome do país.'], 400);
        }
        $pais = buscarPais($nome);
        if ($pais === null) {
            responderJson(['erro' => $erroApi !== '' ? $erroApi : 'País não encontrado.'], 404);
        }
        $pais['nome'] = $pais['nome_api'];
        responderJson(['pais' => $pais]);
    }

    // ---- Países cadastrados no banco ----
    require_once __DIR__ . '/../../conexao.php';

    $sql = "SELECT id, nome, populacao, area, idioma, moeda, regime_politico
            FROM paises ORDER BY nome";
    $resultado = mysqli_query($conn, $sql);

    if (!$resultado) {
        responderJson(['erro' => 'Erro ao consultar o banco de dados: ' . mysqli_error($conn)], 500);
    }

    $paises = [];
    $naoEncontrados = [];

    while ($linha = mysqli_fetch_assoc($resultado)) {
        $info = buscarPais($linha['nome']);

        if ($info === null) {
            $naoEncontrados[] = $linha['nome'];
            continue;
        }

        $paises[] = [
            'id'        => (int) $linha['id'],
            'nome'      => $linha['nome'],
            'capital'   => $info['capital'],
            'lat'       => $info['lat'],
            'lng'       => $info['lng'],
            'bandeira'  => $info['bandeira'],
            'regiao'    => $info['regiao'],
            // Dados do banco (o que foi cadastrado no CRUD)
            'populacao' => $linha['populacao'] !== null ? (int) $linha['populacao'] : $info['populacao'],
            'idioma'    => $linha['idioma'],
            'moeda'     => $linha['moeda'],
            'regime'    => $linha['regime_politico'],
        ];
    }

    responderJson([
        'paises'          => $paises,
        'nao_encontrados' => $naoEncontrados,
        'diagnostico'     => trim($erroApi . ' ' . implode('; ', $avisosPhp)),
    ]);
}

// ===================== MODO PÁGINA =====================
$chaveConfigurada = ($googleMapsKey !== "" && $googleMapsKey !== "SUA_CHAVE_API");
$rcConfigurada    = restCountriesConfigurada();

$pageTitle = "Mapa-Múndi";
include __DIR__ . '/partials/header.php';
?>

<h2 class="mb-1">Mapa-Múndi</h2>
<p class="text-secondary mb-4">
Países cadastrados no sistema, localizados pela API REST Countries.
</p>

<?php if (!$rcConfigurada) { ?>
<div class="alert alert-warning">
Chave da REST Countries não configurada. A API antiga foi desativada e a v5 exige uma chave
(grátis em <a href="https://restcountries.com/sign-up" target="_blank" rel="noopener">restcountries.com/sign-up</a>).
Informe a chave em <code>config/restcountries.php</code>.
</div>
<?php } ?>

<form id="formBusca" class="input-group mb-3">
<input type="text" id="campoBusca" class="form-control"
placeholder="Pesquisar qualquer país na REST Countries (ex.: Japão)" required>
<button class="btn btn-outline-light">Localizar</button>
</form>

<p id="statusMapa" class="text-secondary small mb-2">Carregando países...</p>

<div class="card shadow mb-3">
<div class="card-body p-2">

<?php if (!$chaveConfigurada) { ?>
<div class="alert alert-warning mb-0">
Chave da API do Google Maps não configurada. Informe a chave em
<code>config/google_maps.php</code>.
</div>
<?php } else { ?>
<div id="mapa" style="width:100%; height:550px; border-radius:.375rem;"></div>
<?php } ?>

</div>
</div>

<?php if ($chaveConfigurada) { ?>
<script>
const rcConfigurada = <?= json_encode($rcConfigurada) ?>;
let mapa;
let marcadorBusca = null;
let janelaAberta = null;
const statusMapa = document.getElementById("statusMapa");

// Escapa texto antes de inserir no HTML do InfoWindow (evita XSS)
function escapar(texto) {
    const div = document.createElement("div");
    div.textContent = (texto === null || texto === undefined) ? "" : texto;
    return div.innerHTML;
}

// Lê a resposta como texto para mostrar o erro real se não vier JSON
async function lerJson(resp) {
    const texto = await resp.text();
    let dados;
    try {
        dados = JSON.parse(texto);
    } catch (e) {
        throw new Error("resposta inválida do servidor: " + texto.replace(/<[^>]*>/g, " ").trim().slice(0, 300));
    }
    if (!resp.ok) throw new Error(dados.erro || "Falha na requisição");
    return dados;
}

function montarInfo(p) {
    const pop = (p.populacao !== null && p.populacao !== undefined)
        ? Number(p.populacao).toLocaleString("pt-BR") : "—";
    const bandeira = p.bandeira
        ? '<img src="' + escapar(p.bandeira) + '" alt="Bandeira" style="width:60px;border:1px solid #ccc;display:block;margin-bottom:6px;">'
        : "";
    let html = '<div style="color:#000">' + bandeira +
        "<strong>" + escapar(p.nome) + "</strong><br>" +
        "Capital: " + (escapar(p.capital) || "—") + "<br>" +
        "Região: " + (escapar(p.regiao) || "—") + "<br>" +
        "População: " + pop;
    if (p.idioma) html += "<br>Idioma: " + escapar(p.idioma);
    if (p.moeda)  html += "<br>Moeda: " + escapar(p.moeda);
    if (p.regime) html += "<br>Regime: " + escapar(p.regime);
    return html + "</div>";
}

function abrirJanela(marcador, p) {
    if (janelaAberta) janelaAberta.close();
    janelaAberta = new google.maps.InfoWindow({ content: montarInfo(p) });
    janelaAberta.open({ anchor: marcador, map: mapa });
}

// 1) Países cadastrados no banco (?api=1 -> REST Countries v5)
async function carregarPaises() {
    if (!rcConfigurada) {
        statusMapa.textContent = "Configure a chave da REST Countries em config/restcountries.php.";
        return;
    }
    try {
        const dados = await lerJson(await fetch("mapa_paises.php?api=1"));
        const limites = new google.maps.LatLngBounds();

        dados.paises.forEach(function (p) {
            const posicao = { lat: p.lat, lng: p.lng };
            const marcador = new google.maps.Marker({
                position: posicao, map: mapa, title: p.nome
            });
            marcador.addListener("click", function () { abrirJanela(marcador, p); });
            limites.extend(posicao);
        });

        if (dados.paises.length > 1) {
            mapa.fitBounds(limites);
        } else if (dados.paises.length === 1) {
            mapa.setCenter(limites.getCenter());
            mapa.setZoom(5);
        }

        let msg = dados.paises.length + " país(es) exibido(s) no mapa.";
        if (dados.nao_encontrados.length > 0) {
            msg += " Não localizados na API: " + dados.nao_encontrados.join(", ") + ".";
            if (dados.diagnostico) msg += " Detalhe do erro: " + dados.diagnostico;
        }
        statusMapa.textContent = msg;
    } catch (erro) {
        statusMapa.textContent = "Erro ao carregar o mapa: " + erro.message;
    }
}

// 2) Pesquisa de um país na REST Countries (não grava no banco)
document.getElementById("formBusca").addEventListener("submit", async function (e) {
    e.preventDefault();
    const nome = document.getElementById("campoBusca").value.trim();
    if (!nome) return;
    if (!rcConfigurada) {
        statusMapa.textContent = "Configure a chave da REST Countries em config/restcountries.php.";
        return;
    }

    statusMapa.textContent = 'Buscando "' + nome + '"...';
    try {
        const dados = await lerJson(await fetch("mapa_paises.php?api=buscar&q=" + encodeURIComponent(nome)));
        const p = dados.pais;

        if (marcadorBusca) marcadorBusca.setMap(null);
        marcadorBusca = new google.maps.Marker({
            position: { lat: p.lat, lng: p.lng }, map: mapa, title: p.nome,
            icon: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png"
        });
        marcadorBusca.addListener("click", function () { abrirJanela(marcadorBusca, p); });
        mapa.panTo({ lat: p.lat, lng: p.lng });
        mapa.setZoom(5);
        abrirJanela(marcadorBusca, p);
        statusMapa.textContent = "Exibindo: " + p.nome;
    } catch (erro) {
        statusMapa.textContent = "Erro na busca: " + erro.message;
    }
});

function iniciarMapa() {
    mapa = new google.maps.Map(document.getElementById("mapa"), {
        zoom: 2,
        center: { lat: 20, lng: 0 }
    });
    carregarPaises();
}
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode($googleMapsKey) ?>&callback=iniciarMapa&loading=async"
    async>
</script>
<?php } ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
