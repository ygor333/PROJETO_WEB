<?php
require_once __DIR__ . '/../login/verificar_sessao.php';
require_once __DIR__ . '/../../config/google_maps.php';

// Dados do local exibido no mapa: sede da Etec de São José dos Campos
$local     = "Etec Profª Ilza Nascimento Pintus";
$endereco  = "Av. Salmão, 570 - Parque Residencial Aquarius, CEP 12246-260";
$cidade    = "São José dos Campos - SP";
$latitude  = -23.2194;
$longitude = -45.9068;

// Link para abrir o endereço no Google Maps (busca pelo texto do endereço)
$linkMaps = "https://www.google.com/maps/search/?api=1&query="
          . urlencode("$local, $endereco, $cidade");

$chaveConfigurada = ($googleMapsKey !== "" && $googleMapsKey !== "SUA_CHAVE_API");

$pageTitle = "Mapa";
include __DIR__ . '/partials/header.php';
?>

<h2 class="mb-1">Localização no Mapa</h2>
<p class="text-secondary mb-4">
<strong><?= htmlspecialchars($local) ?></strong><br>
<?= htmlspecialchars($endereco) ?> — <?= htmlspecialchars($cidade) ?>
</p>

<div class="card shadow mb-3">
<div class="card-body p-2">

<?php if (!$chaveConfigurada) { ?>
<div class="alert alert-warning mb-0">
Chave da API do Google Maps não configurada. Informe a chave em
<code>config/google_maps.php</code>.
</div>
<?php } else { ?>
<div id="mapa" style="width:100%; height:500px; border-radius:.375rem;"></div>
<?php } ?>

</div>
</div>

<a href="<?= htmlspecialchars($linkMaps) ?>" target="_blank" rel="noopener"
class="btn btn-outline-light btn-sm">Abrir no Google Maps</a>

<?php if ($chaveConfigurada) { ?>
<script>
function iniciarMapa() {
    const localizacao = {
        lat: <?= json_encode($latitude) ?>,
        lng: <?= json_encode($longitude) ?>
    };

    const mapa = new google.maps.Map(
        document.getElementById("mapa"),
        {
            zoom: 16,
            center: localizacao
        }
    );

    const marcador = new google.maps.Marker({
        position: localizacao,
        map: mapa,
        title: <?= json_encode($local) ?>
    });

    // Janela de informações exibida ao clicar no marcador
    const janela = new google.maps.InfoWindow({
        content: "<strong>" + <?= json_encode(htmlspecialchars($local)) ?> + "</strong><br>" +
                 <?= json_encode(htmlspecialchars($endereco)) ?>
    });
    marcador.addListener("click", function () {
        janela.open({ anchor: marcador, map: mapa });
    });
}
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode($googleMapsKey) ?>&callback=iniciarMapa&loading=async"
    async>
</script>
<?php } ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
