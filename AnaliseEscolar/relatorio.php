<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Relatório Escolar</title>
<link rel="stylesheet" href="./relatorio.css">

</head>

<body>

<div class="container">

<div class="relatorio">

</head>

<body>

<div class="container">

<div class="relatorio">

<?php

if(
    isset($_POST['turma']) &&
    isset($_POST['qtd'])
){

    $turma = $_POST['turma'];
    $qtd = $_POST['qtd'];

    echo "<h1>Resultados da Turma: $turma</h1>";

    // VARIÁVEIS GERAIS
    $somaMedias = 0;
    $maiorMedia = 0;
    $menorMedia = 10;
    $aprovados = 0;
    $recuperacao = 0;
    $reprovados = 0;
    $somaNotas = 0;

    for($i = 1; $i <= $qtd; $i++){

        $nome = $_POST["nome$i"];

        $p1 = floatval($_POST["p1_$i"]);
        $p2 = floatval($_POST["p2_$i"]);
        $trab = floatval($_POST["trab_$i"]);

        // SOMA TOTAL DAS NOTAS
        $somaNotas += ($p1 + $p2 + $trab);

        // MÉDIA DO ALUNO
        $media = ($p1 + $p2 + $trab) / 3;

        // SOMA DAS MÉDIAS
        $somaMedias += $media;

        // MAIOR MÉDIA
        if($media > $maiorMedia){
            $maiorMedia = $media;
        }

        // MENOR MÉDIA
        if($media < $menorMedia){
            $menorMedia = $media;
        }

        // SITUAÇÃO
        if($media >= 7){

            $situacao = "<span class='aprovado'>Aprovado</span>";
            $aprovados++;

        }elseif($media >= 5){

            $situacao = "<span class='recuperacao'>Recuperação</span>";
            $recuperacao++;

        }else{

            $situacao = "<span class='reprovado'>Reprovado</span>";
            $reprovados++;
        }

        // EXIBIÇÃO DO ALUNO
        echo "
        <div class='aluno'>

            <h3>$nome</h3>

            <p><strong>P1:</strong> $p1</p>

            <p><strong>P2:</strong> $p2</p>

            <p><strong>Trabalho:</strong> $trab</p>

            <p>
                <strong>Média:</strong>
                ".number_format($media,2)."
            </p>

            <p>
                <strong>Situação:</strong>
                $situacao
            </p>

        </div>
        ";
    }

    // MÉDIA GERAL
    $mediaGeral = $somaMedias / $qtd;

    // PERCENTUAL DE APROVAÇÃO
    $percentualAprovacao = ($aprovados / $qtd) * 100;

    // RESUMO FINAL
    echo "

    <div class='resumo'>

        <h2>Resumo da Turma</h2>

        <p>
            <strong>1. Média geral da turma:</strong>
            ".number_format($mediaGeral,2)."
        </p>

        <p>
            <strong>2. Maior média encontrada:</strong>
            ".number_format($maiorMedia,2)."
        </p>

        <p>
            <strong>3. Menor média encontrada:</strong>
            ".number_format($menorMedia,2)."
        </p>

        <p>
            <strong>4. Quantidade de aprovados:</strong>
            $aprovados
        </p>

        <p>
            <strong>5. Quantidade de recuperações:</strong>
            $recuperacao
        </p>

        <p>
            <strong>6. Quantidade de reprovados:</strong>
            $reprovados
        </p>

        <p>
            <strong>7. Percentual de aprovação da turma:</strong>
            ".number_format($percentualAprovacao,2)."%
        </p>

        <p>
            <strong>8. Soma total de todas as notas lançadas:</strong>
            ".number_format($somaNotas,2)."
        </p>

    </div>

    ";

}else{

    echo "<h1>Nenhum dado recebido.</h1>";

}

?>

</div>

</div>

</body>
</html>
