<?php
// gerenciar_usuarios.php - Só acessível a quem já está logado.
// Permite cadastrar novos usuários (com senha provisória) e desbloquear contas.

require_once __DIR__ . '/verificar_sessao.php';
require_once __DIR__ . '/../../conexao.php';

$erro = '';
$sucesso = '';

// Cadastro de novo usuário
if (isset($_POST['salvar'])) {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || strlen($senha) < 8) {
        $erro = 'Preencha nome, e-mail e uma senha provisória com pelo menos 8 caracteres.';
    } else {
        $verifica = mysqli_prepare($conn, 'SELECT id_usuario FROM usuarios WHERE email = ?');
        mysqli_stmt_bind_param($verifica, 's', $email);
        mysqli_stmt_execute($verifica);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($verifica))) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            // primeiro_acesso = 1 -> usuário é obrigado a trocar a senha no primeiro login
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO usuarios (nome, email, senha, sts_cadastro, tentativas_login, primeiro_acesso)
                 VALUES (?, ?, ?, 'A', 0, 1)"
            );
            mysqli_stmt_bind_param($stmt, 'sss', $nome, $email, $hash);
            mysqli_stmt_execute($stmt);
            $sucesso = 'Usuário cadastrado com sucesso. No primeiro login ele será obrigado a trocar a senha.';
        }
    }
}

// Desbloqueio de conta (zera tentativas e volta sts_cadastro para 'A')
if (isset($_GET['desbloquear'])) {
    $id = (int) $_GET['desbloquear'];

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE usuarios SET sts_cadastro = 'A', tentativas_login = 0 WHERE id_usuario = ?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $log = mysqli_prepare(
        $conn,
        "INSERT INTO logs (id_usuario, acao, descricao) VALUES (?, 'desbloqueio', 'Conta desbloqueada manualmente.')"
    );
    mysqli_stmt_bind_param($log, 'i', $id);
    mysqli_stmt_execute($log);

    $sucesso = 'Usuário desbloqueado com sucesso.';
}

$usuarios = mysqli_query($conn, 'SELECT id_usuario, nome, email, sts_cadastro, tentativas_login, primeiro_acesso FROM usuarios ORDER BY nome');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Usuários - CRUD Mundo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<nav class="navbar navbar-dark bg-dark border-bottom border-secondary mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="../g.paises/index.php">CRUD Mundo</a>
    <div>
      <a class="btn btn-outline-light btn-sm me-2" href="../g.paises/index.php">Menu</a>
      <a class="btn btn-outline-danger btn-sm" href="logout.php">Sair</a>
    </div>
  </div>
</nav>

<div class="container pb-5">

<div class="card shadow mb-4">
<div class="card-body">
<h2 class="card-title h4 mb-3">Cadastrar usuário</h2>

<?php if ($erro): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
<?php endif; ?>

<?php if ($sucesso): ?>
<div class="alert alert-success"><?php echo htmlspecialchars($sucesso); ?></div>
<?php endif; ?>

<form method="POST" class="row g-3">

<div class="col-md-4">
<label class="form-label">Nome</label>
<input type="text" name="nome" class="form-control" placeholder="Nome completo" required>
</div>

<div class="col-md-4">
<label class="form-label">E-mail</label>
<input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
</div>

<div class="col-md-4">
<label class="form-label">Senha provisória</label>
<input type="password" name="senha" class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8">
</div>

<div class="col-12">
<button name="salvar" class="btn btn-primary">Cadastrar</button>
</div>

</form>
</div>
</div>

<div class="card shadow">
<div class="card-body">
<h2 class="card-title h4 mb-3">Usuários cadastrados</h2>

<div class="table-responsive">
<table class="table table-striped align-middle">
<thead>
<tr>
<th>Nome</th>
<th>E-mail</th>
<th>Status</th>
<th>Tentativas</th>
<th>1º acesso pendente</th>
<th>Ações</th>
</tr>
</thead>
<tbody>
<?php while ($u = mysqli_fetch_assoc($usuarios)): ?>
<tr>
<td><?php echo htmlspecialchars($u['nome']); ?></td>
<td><?php echo htmlspecialchars($u['email']); ?></td>
<td>
<?php if ($u['sts_cadastro'] === 'A'): ?>
<span class="badge text-bg-success">Liberado</span>
<?php else: ?>
<span class="badge text-bg-danger">Bloqueado</span>
<?php endif; ?>
</td>
<td><?php echo (int) $u['tentativas_login']; ?></td>
<td><?php echo $u['primeiro_acesso'] ? 'Sim' : 'Não'; ?></td>
<td>
<?php if ($u['sts_cadastro'] === 'B'): ?>
<a href="?desbloquear=<?php echo (int) $u['id_usuario']; ?>"
   class="btn btn-sm btn-outline-success"
   onclick="return confirm('Desbloquear este usuário?')">Desbloquear</a>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>
</div>

</div>

</body>
</html>