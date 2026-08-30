<?php
// trocar_senha.php - Exibido obrigatoriamente no primeiro acesso do usuário

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../conexao.php';

// Só acessa quem estiver logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if (strlen($novaSenha) < 8) {
        $erro = 'A nova senha deve ter pelo menos 8 caracteres.';
    } elseif ($novaSenha !== $confirmarSenha) {
        $erro = 'As senhas não coincidem.';
    } else {
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $conn,
            'UPDATE usuarios
             SET senha = ?, primeiro_acesso = 0, tentativas_login = 0
             WHERE id_usuario = ?'
        );
        mysqli_stmt_bind_param($stmt, 'si', $hash, $_SESSION['id_usuario']);
        mysqli_stmt_execute($stmt);

        $log = mysqli_prepare(
            $conn,
            "INSERT INTO logs (id_usuario, acao, descricao) VALUES (?, 'troca_senha', 'Senha alterada no primeiro acesso.')"
        );
        mysqli_stmt_bind_param($log, 'i', $_SESSION['id_usuario']);
        mysqli_stmt_execute($log);

        $_SESSION['primeiro_acesso'] = false;

        header('Location: ../g.paises/index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Trocar senha - CRUD Mundo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height:100vh;">

<div class="container">
<div class="row justify-content-center">
<div class="col-12 col-sm-8 col-md-6 col-lg-5">

<h1 class="text-center text-white mb-4">CRUD Mundo</h1>

<div class="card shadow">
<div class="card-body">
<h2 class="card-title h4 mb-2">Troque sua senha</h2>
<p class="text-muted mb-3">Este é o seu primeiro acesso. Por segurança, defina uma nova senha antes de continuar.</p>

<?php if ($erro): ?>
<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($erro); ?></div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">Nova senha</label>
<input type="password" name="nova_senha" class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8">
</div>

<div class="mb-3">
<label class="form-label">Confirmar nova senha</label>
<input type="password" name="confirmar_senha" class="form-control" placeholder="Repita a nova senha" required minlength="8">
</div>

<button name="salvar" class="btn btn-primary w-100">Salvar nova senha</button>

</form>

</div>
</div>

</div>
</div>
</div>

</body>
</html>