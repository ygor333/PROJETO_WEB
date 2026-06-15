
<?php
include("conexao.php");

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
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cidades</title>
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js"></script>
</head>
<body>

<h2>Cadastro de Cidades</h2>

<form method="POST">

<div class="form-field">
    <label class="form-label">Nome</label>
    <input type="text" name="nome" placeholder="Nome" required>
</div>

<div class="form-field">
    <label class="form-label">País</label>
    <select name="pais">
    <?php while($p = mysqli_fetch_assoc($paises)){ ?>
    <option value="<?php echo $p['id']; ?>">
    <?php echo $p['nome']; ?>
    </option>
    <?php } ?>
    </select>
</div>

<div class="form-field">
    <label class="form-label">Governante</label>
    <select name="governante">
    <?php while($g = mysqli_fetch_assoc($governantes)){ ?>
    <option value="<?php echo $g['id']; ?>">
    <?php echo $g['nome']; ?>
    </option>
    <?php } ?>
    </select>
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
    <label class="form-label">Clima</label>
    <input type="text" name="clima" placeholder="Clima">
</div>

<div class="form-field">
    <label class="form-label">Data de Fundação</label>
    <input type="date" name="data">
</div>

<button name="salvar">Salvar</button>

</form>

<form method="GET">
<input type="text" name="buscar" placeholder="Pesquisar cidade">
<button>Buscar</button>
</form>

<table>
<tr>
<th>ID</th>
<th>Nome</th>
<th>População</th>
<th>Área</th>
<th>Clima</th>
<th>Fundação</th>
<th>Ação</th>
</tr>

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
onclick="return confirmarExclusao()">
Excluir
</a>
</td>
</tr>

<?php } ?>

</table>

</body>
</html>
