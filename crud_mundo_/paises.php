
<?php
include("conexao.php");

if(isset($_POST['salvar'])){

$nome = $_POST['nome'];
$continente = $_POST['continente'];
$governante = $_POST['governante'];
$pop = $_POST['populacao'];
$area = $_POST['area'];
$idioma = $_POST['idioma'];
$clima = $_POST['clima'];
$regime = $_POST['regime'];
$moeda = $_POST['moeda'];

$sql = "INSERT INTO paises
(nome,continente_id,governante_id,populacao,area,idioma,clima,regime_politico,moeda)

VALUES

('$nome','$continente','$governante','$pop','$area','$idioma','$clima','$regime','$moeda')";

mysqli_query($conn,$sql);
}

if(isset($_GET['excluir'])){
$id = $_GET['excluir'];
mysqli_query($conn,"DELETE FROM paises WHERE id=$id");
}

$filtro = "";

if(isset($_GET['buscar'])){
$filtro = $_GET['buscar'];
}

$paises = mysqli_query($conn,"SELECT * FROM paises WHERE nome LIKE '%$filtro%'");

$continentes = mysqli_query($conn,"SELECT * FROM continentes");

$governantes = mysqli_query($conn,"SELECT * FROM governantes");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Países</title>
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js"></script>
</head>
<body>

<h2>Cadastro de Países</h2>

<form method="POST">

<div class="form-field">
    <label class="form-label">Nome</label>
    <input type="text" name="nome" placeholder="Nome" required>
</div>

<div class="form-field">
    <label class="form-label">Continente</label>
    <select name="continente">
    <?php while($c = mysqli_fetch_assoc($continentes)){ ?>
    <option value="<?php echo $c['id']; ?>">
    <?php echo $c['nome']; ?>
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
    <label class="form-label">Idioma</label>
    <input type="text" name="idioma" placeholder="Idioma">
</div>

<div class="form-field">
    <label class="form-label">Clima</label>
    <input type="text" name="clima" placeholder="Clima">
</div>

<div class="form-field">
    <label class="form-label">Regime Político</label>
    <input type="text" name="regime" placeholder="Regime Político">
</div>

<div class="form-field">
    <label class="form-label">Moeda</label>
    <input type="text" name="moeda" placeholder="Moeda">
</div>

<button name="salvar">Salvar</button>

</form>

<form method="GET">
<input type="text" name="buscar" placeholder="Pesquisar país">
<button>Buscar</button>
</form>

<table>
<tr>
<th>ID</th>
<th>Nome</th>
<th>População</th>
<th>Área</th>
<th>Idioma</th>
<th>Clima</th>
<th>Regime</th>
<th>Moeda</th>
<th>Ação</th>
</tr>

<?php while($d = mysqli_fetch_assoc($paises)){ ?>

<tr>
<td><?php echo $d['id']; ?></td>
<td><?php echo $d['nome']; ?></td>
<td><?php echo $d['populacao']; ?></td>
<td><?php echo $d['area']; ?></td>
<td><?php echo $d['idioma']; ?></td>
<td><?php echo $d['clima']; ?></td>
<td><?php echo $d['regime_politico']; ?></td>
<td><?php echo $d['moeda']; ?></td>

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
