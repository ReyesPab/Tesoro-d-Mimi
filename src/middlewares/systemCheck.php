<?php
class SystemCheck {
    public static function verificarBloqueo() {
        $lockFile = self::getLockFilePath();
        
        if (file_exists($lockFile)) {
            try {
                $lockData = json_decode(file_get_contents($lockFile), true);
                
                if (self::isApiRequest()) {
                    self::responderJsonBloqueo($lockData);
                } else {
                    self::mostrarPaginaMantenimientoMejorada($lockData);
                }
                exit;
            } catch (\Throwable $e) {
                error_log("Error leyendo archivo de bloqueo: " . $e->getMessage());
            }
        }
    }
    
    private static function isApiRequest() {
        return strpos($_SERVER['REQUEST_URI'], 'route=') !== false ||
               strpos($_SERVER['REQUEST_URI'], '/api/') !== false ||
               !empty($_GET['route']);
    }
    
    private static function getLockFilePath() {
        return dirname(__DIR__, 2) . '/system.lock';
    }
    
    private static function responderJsonBloqueo($lockData) {
        http_response_code(503);
        header('Content-Type: application/json');
        header('Retry-After: 300');
        
        echo json_encode([
            'status' => '503',
            'message' => 'Sistema en mantenimiento - Restauración en curso',
            'data' => [
                'bloqueado_desde' => $lockData['inicio'] ?? 'Desconocido',
                'usuario_responsable' => $lockData['usuario'] ?? 'Sistema',
                'motivo' => $lockData['motivo'] ?? 'Mantenimiento',
                'tiempo_espera' => 'El sistema estará disponible en unos minutos',
                'proximo_disponible' => self::calcularTiempoDisponible($lockData)
            ],
            'timestamp' => time()
        ]);
    }
    
    private static function calcularTiempoDisponible($lockData) {
        $inicio = isset($lockData['timestamp']) ? $lockData['timestamp'] : time();
        $tiempoEstimado = 1800;
        $proximoDisponible = $inicio + $tiempoEstimado;
        
        return date('Y-m-d H:i:s', $proximoDisponible);
    }
    
    private static function mostrarPaginaMantenimientoMejorada($lockData) {
        header('HTTP/1.1 503 Service Unavailable');
        header('Retry-After: 300');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        
        $inicio = isset($lockData['inicio']) ? strtotime($lockData['inicio']) : time();
        $transcurrido = time() - $inicio;
        $minutos = floor($transcurrido / 60);
        
        $tiempoEstimadoMinutos = 30;
        $minutosRestantes = max(1, $tiempoEstimadoMinutos - $minutos);
        $proximoDisponible = time() + ($minutosRestantes * 60);
        
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sistema en Mantenimiento - Tesoro D' MIMI</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
            <style>
                body { 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    margin: 0;
                    padding: 20px;
                }
                .maintenance-card {
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                    max-width: 700px;
                    width: 100%;
                    text-align: center;
                    padding: 2.5rem;
                    position: relative;
                    overflow: hidden;
                }
                .maintenance-card::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 4px;
                    background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1);
                }
                .spinner {
                    width: 80px;
                    height: 80px;
                    border: 8px solid #f3f3f3;
                    border-top: 8px solid #667eea;
                    border-radius: 50%;
                    animation: spin 1.5s linear infinite;
                    margin: 0 auto 30px;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                .pulse {
                    animation: pulse 2s infinite;
                }
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.02); }
                    100% { transform: scale(1); }
                }
                .progress {
                    height: 8px;
                    margin: 20px 0;
                }
                .status-badge {
                    font-size: 0.8rem;
                }
                .time-info {
                    background: #f8f9fa;
                    border-radius: 8px;
                    padding: 15px;
                    margin: 15px 0;
                }
            </style>
        </head>
        <body>
            <div class="maintenance-card pulse">
                <div class="spinner"></div>
                
                <h2 class="text-danger mb-3">
                    <i class="bi bi-database-exclamation me-2"></i> Sistema en Restauración
                </h2>
                
                <div class="alert alert-warning mb-4">
                    <h5 class="alert-heading">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Restauración de Base de Datos en Progreso
                    </h5>
                    <p class="mb-0">
                        Estamos restaurando la base de datos desde una copia de seguridad. 
                        <strong>El sistema estará disponible automáticamente</strong> cuando el proceso termine.
                    </p>
                </div>
                
                <div class="time-info">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <strong>Tiempo transcurrido</strong><br>
                            <span class="h5 text-primary"><?php echo $minutos; ?> min</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Tiempo estimado restante</strong><br>
                            <span class="h5 text-warning"><?php echo $minutosRestantes; ?> min</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Próximo disponible</strong><br>
                            <span class="h6 text-success"><?php echo date('H:i', $proximoDisponible); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="row text-start mb-4">
                    <div class="col-md-6">
                        <strong><i class="bi bi-clock me-1"></i> Inicio:</strong><br>
                        <span class="text-muted"><?php echo $lockData['inicio'] ?? date('Y-m-d H:i:s'); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="bi bi-person me-1"></i> Responsable:</strong><br>
                        <span class="text-muted"><?php echo $lockData['usuario'] ?? 'Administrador del sistema'; ?></span>
                    </div>
                </div>
                
                <div class="row text-start mb-4">
                    <div class="col-12">
                        <strong><i class="bi bi-info-circle me-1"></i> Estado:</strong><br>
                        <span class="text-muted">
                            <span class="badge bg-warning status-badge">EN PROCESO</span>
                            - Restaurando base de datos
                        </span>
                    </div>
                </div>

                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width: <?php echo min(90, ($minutos / $tiempoEstimadoMinutos) * 100); ?>%"
                         aria-valuenow="<?php echo min(90, ($minutos / $tiempoEstimadoMinutos) * 100); ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?php echo round(min(90, ($minutos / $tiempoEstimadoMinutos) * 100)); ?>%
                    </div>
                </div>
                
                <div class="alert alert-info text-start small">
                    <i class="bi bi-lightbulb me-1"></i>
                    <strong>Nota:</strong> Esta operación garantiza la integridad de sus datos. 
                    Serás redirigido automáticamente al inicio de sesión cuando el sistema esté disponible.
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button class="btn btn-primary me-md-2" onclick="reintentarAcceso()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reintentar Ahora
                    </button>
                    <button class="btn btn-outline-secondary" onclick="volverInicio()">
                        <i class="bi bi-box-arrow-right me-1"></i> Ir al Login
                    </button>
                </div>
                
                <div class="mt-3 text-muted small">
                    <i class="bi bi-arrow-repeat me-1"></i> 
                    Reintento automático en <span id="countdown">30</span> segundos
                </div>
            </div>

            <script>
                let countdown = 30;
                const countdownElement = document.getElementById('countdown');
                
                const countdownInterval = setInterval(() => {
                    countdown--;
                    if (countdownElement) {
                        countdownElement.textContent = countdown;
                    }
                    
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        verificarDisponibilidad();
                    }
                }, 1000);
                
                function verificarDisponibilidad() {
                    fetch(window.location.href, { 
                        method: 'GET',
                        cache: 'no-cache',
                        headers: {
                            'Cache-Control': 'no-cache',
                            'Pragma': 'no-cache'
                        }
                    })
                    .then(response => {
                        if (response.status === 200) {
                            window.location.href = '/sistema/public/login';
                        } else {
                            countdown = 30;
                            if (countdownElement) {
                                countdownElement.textContent = countdown;
                            }
                        }
                    })
                    .catch(error => {
                        countdown = 30;
                        if (countdownElement) {
                            countdownElement.textContent = countdown;
                        }
                    });
                }
                
                function reintentarAcceso() {
                    verificarDisponibilidad();
                }
                
                function volverInicio() {
                    window.location.href = '/sistema/public/login';
                }
                
                setInterval(verificarDisponibilidad, 30000);
            </script>
        </body>
        </html>
        <?php
    }
}