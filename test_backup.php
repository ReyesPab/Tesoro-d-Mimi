<?php
require_once 'vendor/autoload.php';

use App\controllers\backupController;

header('Content-Type: text/plain; charset=utf-8');

echo "=== PRUEBA DE BACKUP MANUAL ===\n";

try {
    $result = backupController::crearBackupManual();
    echo "Resultado: " . $result . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}