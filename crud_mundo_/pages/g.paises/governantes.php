<?php
require_once __DIR__ . '/../login/verificar_sessao.php';
include(__DIR__ . "/../../conexao.php");

if(isset($_POST['salvar'])){

$nome = $_POST['nome'];
$partido = $_POST['partido'];
$data = $_POST['data'];
$idade = $_POST['idade'];
$inicio = $_POST['inicio'];
$fim = $_POST['fim'];

// Corrigido: na tabela governantes o campo é `partido`.
// Mantemos o insert coerente com o schema atual (banco.sql).
$sql = "INSERT INTO governantes
(nome,partido,data_nascimento,idade,inicio_mandato,fim_mandato)
VALUES

('$nome','$partido','$data','$idade','$inicio','$fim')";


mysqli_query($conn,$sql);
}
if(isset($_GET['excluir'])){
$id = $_GET['excluir'];
mysqli_query($conn,"DELETE FROM governantes WHERE id=$id");
}


$governantes = mysqli_query($conn,"SELECT * FROM governantes");

$pageTitle = "Governantes";
include __DIR__ . '/partials/header.php';
?>

<h2 class="mb-4">Governantes</h2>

<div class="card shadow mb-4">
<div class="card-body">
<form method="POST">
<div class="row g-3">

<div class="col-md-6">
<label class="form-label">Nome</label>
<input type="text" name="nome" class="form-control" placeholder="Nome" required>
</div>

<div class="col-md-6">
<label class="form-label">Partido</label>
<input type="text" name="partido" class="form-control" placeholder="Partido">
</div>

<div class="col-md-6">
<label class="form-label">Data de Nascimento</label>
<input type="date" name="data" class="form-control" placeholder="Data de Nascimento">
</div>

<div class="col-md-6">
<label class="form-label">Idade</label>
<input type="number" name="idade" class="form-control" placeholder="Idade">
</div>

<div class="col-md-6">
<label class="form-label">Início do Mandato</label>
<input type="date" name="inicio" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">Fim do Mandato</label>
<input type="date" name="fim" class="form-control">
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
<th>Partido</th>
<th>Idade</th>
<th>Ação</th>
</tr>
</thead>
<tbody>

<?php while($d = mysqli_fetch_assoc($governantes)){ ?>

<tr>
<td><?php echo $d['nome']; ?></td>
<td><?php echo $d['partido']; ?></td>
<td><?php echo $d['idade']; ?></td>

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
