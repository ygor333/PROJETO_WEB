<!DOCTYPE html>
<html lang="pt-br">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CRUD Mundo</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial;
}

body{
    background:#f4f4f4;
}

.container{
    width:90%;
    margin:auto;
    padding:20px;
}

h1{
    text-align:center;
    margin-bottom:20px;
}

form{
    background:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:5px;
}

button{
    padding:12px 20px;
    background:#007bff;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

button:hover{
    background:#0056b3;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

table th{
    background:#007bff;
    color:white;
    padding:15px;
}

table td{
    border:1px solid #ccc;
    padding:12px;
    text-align:center;
}

a{
    text-decoration:none;
    padding:8px 12px;
    color:white;
    border-radius:5px;
}

.editar{
    background:orange;
}

.excluir{
    background:red;
}

.pesquisa{
    margin-bottom:20px;
}

</style>

</head>

<body>

<div class="container">

<h1>CRUD Mundo - Países</h1>

<!-- PESQUISA -->

<input
type="text"
id="pesquisa"
class="pesquisa"
placeholder="Pesquisar país..."
onkeyup="pesquisar()">

<!-- FORM -->

<form
action="salvar.php"
method="POST"
onsubmit="return validarFormulario()">

<input
type="text"
name="nome"
id="nome"
placeholder="Nome do país">

<input
type="text"
name="idioma"
placeholder="Idioma">

<input
type="text"
name="moeda"
placeholder="Moeda">

<button type="submit">
Cadastrar
</button>

</form>

<!-- TABELA -->

<table id="tabela">

<tr>
<th>ID</th>
<th>Nome</th>
<th>Idioma</th>
<th>Moeda</th>
<th>Ações</th>
</tr>

<?php

$conexao = mysqli_connect(
    "localhost",
    "root",
    "",
    "bd_mundo"
);

$sql = "SELECT * FROM paises";

$resultado = mysqli_query($conexao, $sql);

while($dados = mysqli_fetch_assoc($resultado)){

?>

<tr>

<td><?= $dados['id_pais'] ?></td>

<td><?= $dados['nome'] ?></td>

<td><?= $dados['idioma'] ?></td>

<td><?= $dados['moeda'] ?></td>

<td>

<a
href="editar.php?id=<?= $dados['id_pais'] ?>"
class="editar">
Editar
</a>

<a
href="excluir.php?id=<?= $dados['id_pais'] ?>"
class="excluir"
onclick="return confirmarExclusao()">
Excluir
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

<script>

// VALIDAR FORMULÁRIO
function validarFormulario(){

    let nome =
    document.getElementById("nome").value;

    if(nome == ""){

        alert("Preencha o nome!");

        return false;

    }

    return true;

}

// CONFIRMAR EXCLUSÃO
function confirmarExclusao(){

    return confirm(
        "Deseja realmente excluir?"
    );

}

// PESQUISA
function pesquisar(){

    let input =
    document.getElementById("pesquisa")
    .value
    .toLowerCase();

    let tabela =
    document.getElementById("tabela");

    let tr =
    tabela.getElementsByTagName("tr");

    for(let i = 1; i < tr.length; i++){

        let td =
        tr[i].getElementsByTagName("td")[1];

        if(td){

            let texto =
            td.textContent || td.innerText;

            if(
                texto.toLowerCase()
                .indexOf(input) > -1
            ){

                tr[i].style.display = "";

            }else{

                tr[i].style.display = "none";

            }

        }

    }

}

</script>

</body>
</html>