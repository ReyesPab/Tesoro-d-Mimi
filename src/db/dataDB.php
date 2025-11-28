<?php

use App\config\errorlogs;
use App\config\responseHTTP;
use App\db\connectionDB;
use Dotenv\Dotenv;

errorlogs::activa_error_logs();

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

// CORREGIDO: Usar nombres consistentes con .env
$host = 'mysql:host=' . $_ENV['IP'] . ';port=' . $_ENV['PORT'] . ';dbname=' . $_ENV['DB'];
$user = $_ENV['USER'];
$password = $_ENV['PASSWORD'];

// Inicializar la conexión
connectionDB::inicializar($host, $user, $password);