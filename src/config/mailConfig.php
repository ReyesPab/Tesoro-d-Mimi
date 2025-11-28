<?php

namespace App\config;

class MailConfig {
    public static function getConfig() {
        return [
            'host' => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
            'port' => $_ENV['SMTP_PORT'] ?? 587,
            'username' => $_ENV['SMTP_USERNAME'] ?? '',
            'password' => $_ENV['SMTP_PASSWORD'] ?? '',
            'from_email' => $_ENV['SMTP_FROM_EMAIL'] ?? 'no-reply@rosquilleria.com',
            'from_name' => $_ENV['SMTP_FROM_NAME'] ?? 'Sistema Rosquilleria',
            'smtp_secure' => 'tls'
        ];
    }
}