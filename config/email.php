<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmailConfirmacaoInscricao($email, $nome, $idInscricao) {
    if (getenv('EMAIL_ENABLED') !== 'true') {
        return false;
    }

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log('E-mail inválido ou ausente: ' . $email);
        return false;
    }

    $smtpHost = getenv('SMTP_HOST');
    $smtpPort = getenv('SMTP_PORT') ?: 587;
    $smtpUser = getenv('SMTP_USER');
    $smtpPass = getenv('SMTP_PASS');

    $mailFrom = getenv('MAIL_FROM') ?: $smtpUser;
    $mailFromName = getenv('MAIL_FROM_NAME') ?: 'Socorro no Pedal 2026';

    if (!$smtpHost || !$smtpUser || !$smtpPass) {
        error_log('SMTP não configurado corretamente.');
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->Port = (int) $smtpPort;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom($mailFrom, $mailFromName);
        $mail->addAddress($email, $nome);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmação de inscrição - Socorro no Pedal 2026';

        $nomeSeguro = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
        $idSeguro = htmlspecialchars($idInscricao, ENT_QUOTES, 'UTF-8');

        $mail->Body = "
            <h2>Inscrição confirmada</h2>

            <p>Olá, <strong>{$nomeSeguro}</strong>!</p>

            <p>Sua inscrição no Socorro no Pedal 2026 foi realizada com sucesso.</p>

            <p><strong>Número da inscrição:</strong> {$idSeguro}</p>

            <p>Guarde esse número para consulta e apresentação, caso necessário.</p>

            <br>

            <p>Atenciosamente,<br>
            Organização do Socorro no Pedal 2026</p>

            <p>Realizado pela Superintendência Municipal de Transporte e Trânsito (SMTT) de Nossa Senhora de Socorro</p>

            <br>

            <p><Strong>Este é um e-mail automático, por favor não responda.<Strong></p>
        ";

        $mail->AltBody = "Olá, {$nome}. Sua inscrição no Socorro no Pedal 2026 foi realizada com sucesso. Número da inscrição: {$idInscricao}.";

        $mail->send();

        return true;
    } catch (Exception $e) {
        error_log('Erro ao enviar e-mail de confirmação: ' . $mail->ErrorInfo);
        return false;
    } catch (Throwable $e) {
        error_log('Erro inesperado ao enviar e-mail: ' . $e->getMessage());
        return false;
    }
}