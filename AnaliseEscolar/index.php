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

<h1> Analise Escolar</h1>


<h2>Cadastro da Turma</h2>

<form method="POST">
    Nome da turma:
    <input type="text" name="turma" required>

    Quantidade de alunos:
    <input type="number" name="qtd" min="1" required>

    <input type="submit" value="Gerar Formulário">
</form>


<script>
document.addEventListener("submit", function(e) {

    const form = e.target;

    // Validação da tela inicial
    if (form.querySelector("input[name='qtd']")) {
        const turma = form.turma.value.trim();
        const qtd = parseInt(form.qtd.value);

        if (turma === "") {
            alert("Digite o nome da turma.");
            e.preventDefault();
            return;
        }

        if (isNaN(qtd) || qtd <= 0) {
            alert("Quantidade de alunos inválida.");
            e.preventDefault();
            return;
        }
    }

    // 🔥 Validação APENAS das notas
    const notas = form.querySelectorAll(
        "input[name^='p1_'], input[name^='p2_'], input[name^='trab_']"
    );

    for (let nota of notas) {
        let valor = parseFloat(nota.value);

        if (isNaN(valor) || valor < 0 || valor > 10) {
            alert("As notas devem estar entre 0 e 10.");
            nota.focus();
            e.preventDefault();
            return;
        }
    }

    // Validação dos nomes
    const nomes = form.querySelectorAll("input[type='text']");

    for (let nome of nomes) {
        if (nome.value.trim() === "" ) {
            alert("Preencha todos os nomes.");
            nome.focus();
            e.preventDefault();
            return;
        }
    }

});
</script>



<?php
if (isset($_POST['qtd']) && isset($_POST['turma'])) {

    $qtd = $_POST['qtd'];
    $turma = $_POST['turma'];

    echo "<h2>Turma: $turma</h2>";
    echo "<form method='POST' action='relatorio.php'>";

    echo "<input type='hidden' name='turma' value='$turma'>";
    echo "<input type='hidden' name='qtd' value='$qtd'>";

    for ($i = 1; $i <= $qtd; $i++) {
        echo "<div class='aluno'>";
        echo "<h3>Aluno $i</h3>";

        echo "Nome:<br>";
        echo "<input type='text' name='nome$i' pattern="[A-Za-zÀ-ÿ\s]+" required><br>"; 

        echo "Nota Prova 1:<br>";
        echo "<input type='number' step='0.1' name='p1_$i' required><br>";

        echo "Nota Prova 2:<br>";
        echo "<input type='number' step='0.1' name='p2_$i' required><br>";

        echo "Nota Trabalho:<br>";
        echo "<input type='number' step='0.1' name='trab_$i' required><br>";

        echo "</div>";
    }

    echo "<input type='submit' value='Gerar Relatorio'>";
    echo "</form>";
}
?>







</script>

</div>

</body>
</html>