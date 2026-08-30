<?php
require_once __DIR__ . '/../login/verificar_sessao.php';
include(__DIR__ . "/../../conexao.php");

if(isset($_POST['salvar'])){

$nome = $_POST['nome'];
$pop = $_POST['populacao'];
$area = $_POST['area'];
$total = $_POST['total'];

$sql = "INSERT INTO continentes
(nome,populacao,area,total_paises)

VALUES

('$nome','$pop','$area','$total')";

mysqli_query($conn,$sql);
}

$continentes = mysqli_query($conn,"SELECT * FROM continentes");

$pageTitle = "Continentes";
include __DIR__ . '/partials/header.php';
?>

<h2 class="mb-4">Continentes</h2>

<div class="card shadow mb-4">
<div class="card-body">
<form method="POST">
<div class="row g-3">

<div class="col-md-6">
<label class="form-label">Nome</label>
<input type="text" name="nome" class="form-control" placeholder="Nome" required>
</div>

<div class="col-md-6">
<label class="form-label">População</label>
<input type="number" name="populacao" class="form-control" placeholder="População">
</div>

<div class="col-md-6">
<label class="form-label">Área km²</label>
<input type="number" step="0.01" name="area" class="form-control" placeholder="Área km²">
</div>

<div class="col-md-6">
<label class="form-label">Total de países</label>
<input type="number" name="total" class="form-control" placeholder="Total de países">
</div>

</div>

<button name="salvar" class="btn btn-primary mt-3">Salvar</button>

</form>
</div>
</div>

<div class="card shadow">
<div class="card-body table-responsive">
<table class="table table-striped table-hover align-middle mb-0">
<thead class="table-primary">
<tr>
<th>Nome</th>
<th>População</th>
<th>Área</th>
<th>Total Países</th>
</tr>
</thead>
<tbody>

<?php while($d = mysqli_fetch_assoc($continentes)){ ?>

<tr>
<td><?php echo $d['nome']; ?></td>
<td><?php echo $d['populacao']; ?></td>
<td><?php echo $d['area']; ?></td>
<td><?php echo $d['total_paises']; ?></td>
</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
