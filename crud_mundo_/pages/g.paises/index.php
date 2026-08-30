<?php
require_once __DIR__ . '/../login/verificar_sessao.php';
$pageTitle = "Início";
include __DIR__ . '/partials/header.php';
?>

<h1 class="text-center mb-2">CRUD Mundo</h1>
<p class="text-center text-secondary mb-5">
Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>
</p>

<div class="row g-4 justify-content-center">

<div class="col-6 col-md-3">
<a href="continentes.php" class="card text-decoration-none shadow h-100">
<div class="card-body text-center">
<h5 class="card-title text-dark mb-0">Continentes</h5>
</div>
</a>
</div>

<div class="col-6 col-md-3">
<a href="governantes.php" class="card text-decoration-none shadow h-100">
<div class="card-body text-center">
<h5 class="card-title text-dark mb-0">Governantes</h5>
</div>
</a>
</div>

<div class="col-6 col-md-3">
<a href="paises.php" class="card text-decoration-none shadow h-100">
<div class="card-body text-center">
<h5 class="card-title text-dark mb-0">Países</h5>
</div>
</a>
</div>

<div class="col-6 col-md-3">
<a href="cidades.php" class="card text-decoration-none shadow h-100">
<div class="card-body text-center">
<h5 class="card-title text-dark mb-0">Cidades</h5>
</div>
</a>
</div>

</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
