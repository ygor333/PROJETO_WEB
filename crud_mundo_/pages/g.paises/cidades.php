<?php
require_once __DIR__ . '/../login/verificar_sessao.php';
include(__DIR__ . "/../../conexao.php");

if(isset($_POST['salvar'])){

$nome = $_POST['nome'];
$pais = $_POST['pais'];
$governante = $_POST['governante'];
$pop = $_POST['populacao'];
$area = $_POST['area'];
$clima = $_POST['clima'];
$data = $_POST['data'];

$sql = "INSERT INTO cidades
(nome,pais_id,governante_id,populacao,area,clima,data_fundacao)

VALUES

('$nome','$pais','$governante','$pop','$area','$clima','$data')";

mysqli_query($conn,$sql);
}

if(isset($_GET['excluir'])){
$id = $_GET['excluir'];
mysqli_query($conn,"DELETE FROM cidades WHERE id=$id");
}

$filtro = "";

if(isset($_GET['buscar'])){
$filtro = $_GET['buscar'];
}

$cidades = mysqli_query($conn,"SELECT * FROM cidades WHERE nome LIKE '%$filtro%'");

$paises = mysqli_query($conn,"SELECT * FROM paises");

$governantes = mysqli_query($conn,"SELECT * FROM governantes");

$pageTitle = "Cidades";
include __DIR__ . '/partials/header.php';
?>

<h2 class="mb-4">Cadastro de Cidades</h2>

<div class="card shadow mb-4">
<div class="card-body">
<form method="POST">
<div class="row g-3">

<div class="col-md-6">
<label class="form-label">Nome</label>
<input type="text" name="nome" class="form-control" placeholder="Nome" required>
</div>

<div class="col-md-6">
<label class="form-label">País</label>
<select name="pais" class="form-select">
<?php while($p = mysqli_fetch_assoc($paises)){ ?>
<option value="<?php echo $p['id']; ?>">
<?php echo $p['nome']; ?>
</option>
<?php } ?>
</select>
</div>

<div class="col-md-6">
<label class="form-label">Governante</label>
<select name="governante" class="form-select">
<?php while($g = mysqli_fetch_assoc($governantes)){ ?>
<option value="<?php echo $g['id']; ?>">
<?php echo $g['nome']; ?>
</option>
<?php } ?>
</select>
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
<label class="form-label">Clima</label>
<input type="text" name="clima" class="form-control" placeholder="Clima">
</div>

<div class="col-md-6">
<label class="form-label">Data de Fundação</label>
<input type="date" name="data" class="form-control">
</div>

</div>

<button name="salvar" class="btn btn-primary mt-3">Salvar</button>

</form>
</div>
</div>

<form method="GET" class="input-group mb-4">
<input type="text" name="buscar" class="form-control" placeholder="Pesquisar cidade">
<button class="btn btn-outline-light">Buscar</button>
</form>

<div class="card shadow">
<div class="card-body table-responsive">
<table class="table table-striped table-hover align-middle mb-0">
<thead class="table-primary">
<tr>
<th>ID</th>
<th>Nome</th>
<th>População</th>
<th>Área</th>
<th>Clima</th>
<th>Fundação</th>
<th>Ação</th>
</tr>
</thead>
<tbody>

<?php while($d = mysqli_fetch_assoc($cidades)){ ?>

<tr>
<td><?php echo $d['id']; ?></td>
<td><?php echo $d['nome']; ?></td>
<td><?php echo $d['populacao']; ?></td>
<td><?php echo $d['area']; ?></td>
<td><?php echo $d['clima']; ?></td>
<td><?php echo $d['data_fundacao']; ?></td>

<td>
<a href="?excluir=<?php echo $d['id']; ?>"
class="btn btn-sm btn-outline-danger"
onclick="return confirmarExclusao()">
Excluir
</a>
</td>
</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
