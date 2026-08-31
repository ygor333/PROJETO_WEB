<?php
// header.php - Cabeçalho padrão (Bootstrap) de todas as páginas do sistema.
// Requer que $pageTitle esteja definido antes do include.
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $pageTitle; ?> - CRUD Mundo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<nav class="navbar navbar-dark bg-dark border-bottom border-secondary mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">CRUD Mundo</a>
    <div>
      <a class="btn btn-outline-light btn-sm me-2" href="index.php">Menu</a>
      <a class="btn btn-outline-light btn-sm me-2" href="../login/gerenciar_usuarios.php">Usuários</a>
      <a class="btn btn-outline-danger btn-sm" href="../login/logout.php">Sair</a>
    </div>
  </div>
</nav>

<div class="container pb-5">
