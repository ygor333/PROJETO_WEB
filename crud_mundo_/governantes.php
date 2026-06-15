
<?php
include("conexao.php");

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
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Governantes</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Governantes</h2>

<form method="POST">

<div class="form-field">
    <label class="form-label">Nome</label>
    <input type="text" name="nome" placeholder="Nome" required>
</div>

<div class="form-field">
    <label class="form-label">Partido</label>
    <input type="text" name="partido" placeholder="Partido">
</div>

<div class="form-field">
    <label class="form-label">Data de Nascimento</label>
    <input type="date" name="data" placeholder="Data de Nascimento">
</div>

<div class="form-field">
    <label class="form-label">Idade</label>
    <input type="number" name="idade" placeholder="Idade">
</div>

<div class="form-field">
    <label class="form-label">Início do Mandato</label>
    <input type="date" name="inicio">
</div>

<div class="form-field">
    <label class="form-label">Fim do Mandato</label>
    <input type="date" name="fim">
</div>

<button name="salvar">Salvar</button>

</form>


<table>
<tr>
<th>Nome</th>
<th>Partido</th>
<th>Idade</th>
<th>Ação</th>
</tr>

<?php while($d = mysqli_fetch_assoc($governantes)){ ?>

<tr>
<td><?php echo $d['nome']; ?></td>
<td><?php echo $d['partido']; ?></td>
<td><?php echo $d['idade']; ?></td>


<td>
<a href="?excluir=<?php echo $d['id']; ?>"
onclick="return confirmarExclusao()">
Excluir
</a>
</td>
</tr>

<?php } ?>

</table>

</body>
</html>
