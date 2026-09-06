<?php
// resetar_senha.php - Consome o token gerado em esqueci_senha.php e define nova senha

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../conexao.php';

$erro = '';
$sucesso = false;
$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$tokenHash = $token ? hash('sha256', $token) : '';

$usuario = null;
if ($tokenHash) {
    $stmt = mysqli_prepare(
        $conn,
        'SELECT id_usuario FROM usuarios WHERE token_recuperacao_hash = ? AND token_expira > NOW()'
    );
    mysqli_stmt_bind_param($stmt, 's', $tokenHash);
    mysqli_stmt_execute($stmt);
    $usuario = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if (!$token || !$usuario) {
    $erro = 'Link inválido ou expirado. Solicite uma nova recuperação de senha.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if (strlen($novaSenha) < 8) {
        $erro = 'A nova senha deve ter pelo menos 8 caracteres.';
    } elseif ($novaSenha !== $confirmarSenha) {
        $erro = 'As senhas não coincidem.';
    } else {
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $upd = mysqli_prepare(
            $conn,
            'UPDATE usuarios
             SET senha = ?, tentativas_login = 0, sts_cadastro = \'A\',
                 token_recuperacao_hash = NULL, token_expira = NULL
             WHERE id_usuario = ?'
        );
        mysqli_stmt_bind_param($upd, 'si', $hash, $usuario['id_usuario']);
        mysqli_stmt_execute($upd);

        $sucesso = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Redefinir senha - CRUD Mundo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height:100vh;">

<div class="container">
<div class="row justify-content-center">
<div class="col-12 col-sm-8 col-md-5 col-lg-4">

<h1 class="text-center text-white mb-4">CRUD Mundo</h1>

<div class="card shadow">
<div class="card-body">
<h2 class="card-title h4 mb-3">Redefinir senha</h2>

<?php if ($erro): ?>
<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($erro); ?></div>
<?php endif; ?>

<?php if ($sucesso): ?>
<div class="alert alert-success" role="alert">Senha redefinida com sucesso.</div>
<a href="login.php" class="btn btn-primary w-100">Ir para o login</a>
<?php elseif ($usuario): ?>

<form method="POST">
<input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

<div class="mb-3">
<label class="form-label">Nova senha</label>
<input type="password" name="nova_senha" class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8">
</div>

<div class="mb-3">
<label class="form-label">Confirmar nova senha</label>
<input type="password" name="confirmar_senha" class="form-control" placeholder="Repita a nova senha" required minlength="8">
</div>

<button class="btn btn-primary w-100">Salvar nova senha</button>

</form>

<?php else: ?>
<a href="esqueci_senha.php" class="btn btn-primary w-100">Solicitar novo link</a>
<?php endif; ?>

</div>
</div>

</div>
</div>
</div>

</body>
</html>
