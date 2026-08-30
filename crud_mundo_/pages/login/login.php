<?php
// login.php - Tela de autenticação
$pageTitle = "Início";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../conexao.php';

const MAX_TENTATIVAS_LOGIN = 3; // bloqueia após 3 tentativas erradas consecutivas

$erro = '';

// Se já está logado e não precisa trocar senha, manda direto para o sistema
if (isset($_SESSION['id_usuario']) && empty($_SESSION['primeiro_acesso'])) {
    header('Location: ../g.paises/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim(strtolower($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    $stmt = mysqli_prepare(
        $conn,
        'SELECT id_usuario, nome, senha, sts_cadastro, tentativas_login, primeiro_acesso
         FROM usuarios
         WHERE email = ?'
    );
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);

    if (!$usuario) {
        // E-mail não cadastrado: mensagem genérica, não revela se o e-mail existe
        $erro = 'E-mail ou senha inválidos.';

    } elseif ($usuario['sts_cadastro'] === 'B') {
        $erro = 'Conta bloqueada após 3 tentativas incorretas. Procure o administrador para desbloquear.';

    } elseif (password_verify($senha, $usuario['senha'])) {

        // ---- Login correto ----
        $upd = mysqli_prepare($conn, 'UPDATE usuarios SET tentativas_login = 0 WHERE id_usuario = ?');
        mysqli_stmt_bind_param($upd, 'i', $usuario['id_usuario']);
        mysqli_stmt_execute($upd);

        registrarLog($conn, (int) $usuario['id_usuario'], 'login_sucesso', 'Login realizado com sucesso.');

        session_regenerate_id(true);
        $_SESSION['id_usuario']     = $usuario['id_usuario'];
        $_SESSION['nome']           = $usuario['nome'];
        $_SESSION['primeiro_acesso'] = (int) $usuario['primeiro_acesso'] === 1;

        if ($_SESSION['primeiro_acesso']) {
            header('Location: trocar_senha.php');
        } else {
            header('Location: ../g.paises/index.php');
        }
        exit;

    } else {

        // ---- Senha incorreta ----
        $tentativas = (int) $usuario['tentativas_login'] + 1;

        if ($tentativas >= MAX_TENTATIVAS_LOGIN) {
            $upd = mysqli_prepare(
                $conn,
                "UPDATE usuarios SET tentativas_login = ?, sts_cadastro = 'B' WHERE id_usuario = ?"
            );
            mysqli_stmt_bind_param($upd, 'ii', $tentativas, $usuario['id_usuario']);
            mysqli_stmt_execute($upd);

            registrarLog($conn, (int) $usuario['id_usuario'], 'bloqueio', 'Conta bloqueada após 3 tentativas incorretas.');

            $erro = 'Conta bloqueada após 3 tentativas incorretas. Procure o administrador para desbloquear.';
        } else {
            $upd = mysqli_prepare($conn, 'UPDATE usuarios SET tentativas_login = ? WHERE id_usuario = ?');
            mysqli_stmt_bind_param($upd, 'ii', $tentativas, $usuario['id_usuario']);
            mysqli_stmt_execute($upd);

            registrarLog($conn, (int) $usuario['id_usuario'], 'login_falha', "Tentativa {$tentativas} de " . MAX_TENTATIVAS_LOGIN . ".");

            $erro = "E-mail ou senha inválidos. Tentativa {$tentativas} de " . MAX_TENTATIVAS_LOGIN . '.';
        }
    }
}

function registrarLog(mysqli $conn, int $idUsuario, string $acao, string $descricao): void
{
    $stmt = mysqli_prepare(
        $conn,
        'INSERT INTO logs (id_usuario, acao, descricao) VALUES (?, ?, ?)'
    );
    mysqli_stmt_bind_param($stmt, 'iss', $idUsuario, $acao, $descricao);
    mysqli_stmt_execute($stmt);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - CRUD Mundo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height:100vh;">

<div class="container">
<div class="row justify-content-center">
<div class="col-12 col-sm-8 col-md-5 col-lg-4">

<h1 class="text-center text-white mb-4">CRUD Mundo</h1>

<div class="card shadow">
<div class="card-body">
<h2 class="card-title h4 mb-3">Entrar</h2>

<?php if ($erro): ?>
<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($erro); ?></div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">E-mail</label>
<input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
</div>

<div class="mb-3">
<label class="form-label">Senha</label>
<input type="password" name="senha" class="form-control" placeholder="Senha" required>
</div>

<button name="entrar" class="btn btn-primary w-100">Entrar</button>

</form>

</div>
</div>

</div>
</div>
</div>

</body>
</html>