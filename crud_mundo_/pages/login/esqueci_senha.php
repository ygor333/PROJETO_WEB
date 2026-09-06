<?php
// esqueci_senha.php - Tela de recuperação de senha (gera link de redefinição)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../conexao.php';

const TOKEN_RECUPERACAO_VALIDADE_MINUTOS = 30;

$erro = '';
$linkRecuperacao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim(strtolower($_POST['email'] ?? ''));

    $stmt = mysqli_prepare($conn, 'SELECT id_usuario FROM usuarios WHERE email = ?');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $usuario = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    // Mesmo se o e-mail não existir, seguimos como se tivesse enviado
    // (evita que alguém descubra quais e-mails estão cadastrados)
    if ($usuario) {
        $tokenPlano = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $tokenPlano);
        $expira = (new DateTime())
            ->modify('+' . TOKEN_RECUPERACAO_VALIDADE_MINUTOS . ' minutes')
            ->format('Y-m-d H:i:s');

        $upd = mysqli_prepare(
            $conn,
            'UPDATE usuarios SET token_recuperacao_hash = ?, token_expira = ? WHERE id_usuario = ?'
        );
        mysqli_stmt_bind_param($upd, 'ssi', $tokenHash, $expira, $usuario['id_usuario']);
        mysqli_stmt_execute($upd);

        // Sistema não possui envio de e-mail configurado: o link é exibido
        // na própria tela para fins de demonstração/teste.
        $linkRecuperacao = 'resetar_senha.php?token=' . $tokenPlano;
    }

    if (!$linkRecuperacao) {
        $erro = 'Se o e-mail existir, um link de recuperação foi gerado.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Esqueci minha senha - CRUD Mundo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height:100vh;">

<div class="container">
<div class="row justify-content-center">
<div class="col-12 col-sm-8 col-md-5 col-lg-4">

<h1 class="text-center text-white mb-4">CRUD Mundo</h1>

<div class="card shadow">
<div class="card-body">
<h2 class="card-title h4 mb-2">Recuperar senha</h2>
<p class="text-muted mb-3">Informe seu e-mail para gerar um link de redefinição de senha.</p>

<?php if ($erro): ?>
<div class="alert alert-info" role="alert"><?php echo htmlspecialchars($erro); ?></div>
<?php endif; ?>

<?php if ($linkRecuperacao): ?>
<div class="alert alert-success" role="alert">
Link de redefinição gerado (válido por <?php echo TOKEN_RECUPERACAO_VALIDADE_MINUTOS; ?> minutos).<br>
<a href="<?php echo htmlspecialchars($linkRecuperacao); ?>"><?php echo htmlspecialchars($linkRecuperacao); ?></a>
</div>
<?php else: ?>
<form method="POST">

<div class="mb-3">
<label class="form-label">E-mail</label>
<input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
</div>

<button class="btn btn-primary w-100">Gerar link de recuperação</button>

</form>
<?php endif; ?>

<p class="text-center mt-3 mb-0">
<a href="login.php" class="link-light">Voltar para o login</a>
</p>

</div>
</div>

</div>
</div>
</div>

</body>
</html>
