<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro de Turma</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient( rgb(226, 226, 226), rgb(47, 21, 99)    );
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.container {
    width: 90%;
    max-width: 500px;
    margin: 100px;
}

form {
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

input {
    padding: 5px 5px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

input:focus {
    border-color: #66a6ff;
    box-shadow: 0 0 5px #66a6ff;
}
    
h1{

    text-align: center;

}

 h3{
    margin: 5px 0;
}

h2 {
    margin: 20px 0;
    text-align: center;
    margin-top: 50px
}

.aluno {
    padding: 10px;
    border: 1px solid #eee;
    border-radius: 10px;
    margin-bottom: 10px;
}
</style>
</head>

<body>

<div class="container">

<?php

$turma = $_POST['turma'];
$qtd = $_POST['qtd'];

echo "<h2>Resultados da Turma: $turma</h2>";

for ($i = 1; $i <= $qtd; $i++) {

    $nome = $_POST["nome$i"];
    $p1 = $_POST["p1_$i"];
    $p2 = $_POST["p2_$i"];
    $trab = $_POST["trab_$i"];

    $media = ($p1 + $p2 + $trab) / 3;

    echo "<h3>$nome</h3>";
    echo "Média: " . number_format($media, 2) . "<br><br>";
}
?>





</div>


</body>
</html>