<?php
// verificar_sessao.php - Inclua este arquivo no topo de qualquer página que só pode
// ser acessada por usuários autenticados.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Não logado -> volta para a tela de login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login/login.php');
    exit;
}

// Logado, mas ainda precisa trocar a senha (primeiro acesso) -> força a troca
if (!empty($_SESSION['primeiro_acesso']) && basename($_SERVER['SCRIPT_NAME']) !== 'trocar_senha.php') {
    header('Location: ../login/trocar_senha.php');
    exit;
}
