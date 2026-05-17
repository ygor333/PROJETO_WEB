<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro Escolar</title>
<link rel="stylesheet" href="./index.css">

</head>

<body>

<div class="container">

<h1>Análise Escolar</h1>

<!-- FORMULÁRIO INICIAL -->
<form id="turmaForm">

    <label>Nome da turma:</label>
    <input type="text" id="turma">

    <label>Quantidade de alunos:</label>
    <input type="number" id="qtd" min="1">

    <button type="submit">
        Gerar Formulário
    </button>

</form>

<!-- FORM DOS ALUNOS -->
<div id="areaFormulario"></div>

</div>

<script>

const turmaForm = document.getElementById("turmaForm");
const areaFormulario = document.getElementById("areaFormulario");

turmaForm.addEventListener("submit", function(e){

    e.preventDefault();

    const turma = document.getElementById("turma").value.trim();
    const qtd = parseInt(document.getElementById("qtd").value);

    // VALIDAÇÃO
    if(turma === ""){
        alert("Digite o nome da turma.");
        return;
    }

    if(isNaN(qtd) || qtd <= 0){
        alert("Quantidade inválida.");
        return;
    }

    // GERANDO FORMULÁRIO
    let html = `
    
    <form id="alunosForm" action="relatorio.php" method="POST">

        <input type="hidden" name="turma" value="${turma}">
        <input type="hidden" name="qtd" value="${qtd}">

        <h2>Turma: ${turma}</h2>
    
    `;

    for(let i = 1; i <= qtd; i++){

        html += `

        <div class="aluno">

            <h3>Aluno ${i}</h3>

            <label>Nome:</label>
            <input 
                type="text" 
                name="nome${i}"
                required
            >

            <label>Nota P1:</label>
            <input 
                type="number"
                step="0.1"
                min="0"
                max="10"
                name="p1_${i}"
                required
            >

            <label>Nota P2:</label>
            <input 
                type="number"
                step="0.1"
                min="0"
                max="10"
                name="p2_${i}"
                required
            >

            <label>Nota Trabalho:</label>
            <input 
                type="number"
                step="0.1"
                min="0"
                max="10"
                name="trab_${i}"
                required
            >

        </div>

        `;
    }

    html += `
    
        <button type="submit">
            Enviar para PHP
        </button>

    </form>
    
    `;

    areaFormulario.innerHTML = html;

    // VALIDAÇÃO DO FORM FINAL
    const alunosForm = document.getElementById("alunosForm");

    alunosForm.addEventListener("submit", function(e){

        const inputsNotas = alunosForm.querySelectorAll(
            "input[type='number']"
        );

        for(let nota of inputsNotas){

            const valor = parseFloat(nota.value);

            if(isNaN(valor) || valor < 0 || valor > 10){

                alert("As notas devem estar entre 0 e 10.");

                nota.focus();

                e.preventDefault();

                return;
            }
        }

        const nomes = alunosForm.querySelectorAll(
            "input[type='text']"
        );

        for(let nome of nomes){

            if(nome.value.trim() === ""){

                alert("Preencha todos os nomes.");

                nome.focus();

                e.preventDefault();

                return;
            }
        }

    });

});

</script>

</body>
</html>