<?php
// logout.php - Encerra a sessão do usuário

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id_usuario'])) {
    require_once __DIR__ . '/../../conexao.php';

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO logs (id_usuario, acao, descricao) VALUES (?, 'logout', 'Usuário saiu do sistema.')"
    );
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_usuario']);
    mysqli_stmt_execute($stmt);
}

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
