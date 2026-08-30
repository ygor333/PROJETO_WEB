<?php
// esqueci_senha.php - Gera token de recuperação de senha

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '../conxeao.php';

function solicitarRecuperacao(string $email): array
{
    $email = trim(strtolower($email));
    $pdo = getConexao();

    $stmt = $pdo->prepare('SELECT id_usuario FROM usuario WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch();

    // Mesmo se o e-mail não existir, respondemos como se tivesse enviado
    // (evita que alguém descubra quais e-mails estão cadastrados)
    if (!$usuario) {
        return ['sucesso' => true, 'mensagem' => 'Se o e-mail existir, um link de recuperação foi enviado.'];
    }

    // Token que vai para o e-mail do usuário (não fica salvo em texto puro no banco)
    $tokenPlano = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $tokenPlano);

    $expira = (new DateTime())
        ->modify('+' . TOKEN_RECUPERACAO_VALIDADE_MINUTOS . ' minutes')
        ->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare(
        'UPDATE usuario
         SET token_recuperacao_hash = :hash, token_expira = :expira
         WHERE id_usuario = :id'
    );
    $stmt->execute([
        'hash'   => $tokenHash,
        'expira' => $expira,
        'id'     => $usuario['id_usuario'],
    ]);

    // Link que deve ser enviado por e-mail ao usuário
    $linkRecuperacao = 'https://seusite.com/resetar_senha.php?token=' . $tokenPlano;

    // TODO: enviar $linkRecuperacao por e-mail (ex: PHPMailer, Symfony Mailer, etc.)
    // enviarEmail($email, 'Recuperação de senha', $linkRecuperacao);

    return ['sucesso' => true, 'mensagem' => 'Se o e-mail existir, um link de recuperação foi enviado.'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = solicitarRecuperacao($_POST['email'] ?? '');
    header('Content-Type: application/json');
    echo json_encode($resultado);
}
