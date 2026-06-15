
<?php
include("conexao.php");

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
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Continentes</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Continentes</h2>

<form method="POST">

<div class="form-field">
    <label class="form-label">Nome</label>
    <input type="text" name="nome" placeholder="Nome" required>
</div>

<div class="form-field">
    <label class="form-label">População</label>
    <input type="number" name="populacao" placeholder="População">
</div>

<div class="form-field">
    <label class="form-label">Área km²</label>
    <input type="number" step="0.01" name="area" placeholder="Área km²">
</div>

<div class="form-field">
    <label class="form-label">Total de países</label>
    <input type="number" name="total" placeholder="Total de países">
</div>

<button name="salvar">Salvar</button>

</form>

<table>
<tr>
<th>Nome</th>
<th>População</th>
<th>Área</th>
<th>Total Países</th>
</tr>

<?php while($d = mysqli_fetch_assoc($continentes)){ ?>

<tr>
<td><?php echo $d['nome']; ?></td>
<td><?php echo $d['populacao']; ?></td>
<td><?php echo $d['area']; ?></td>
<td><?php echo $d['total_paises']; ?></td>
</tr>

<?php } ?>

</table>

</body>
</html>
