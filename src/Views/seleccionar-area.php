<?php
session_start();

// Verificar que el usuario esté logueado y tenga rol 2
if (!isset($_SESSION['id_rol']) || intval($_SESSION['id_rol']) !== 2) {
    // Redirigir al inicio si no tiene permisos
    header('Location: /sistema/public/inicio');
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Área</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-image: url('/sistema/src/Views/assets/img/fondorosquillas.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(3px);
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-image {
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        p {
            margin-bottom: 1.5rem;
            color: #555;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background: #ce9c30ff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
            margin: 8px 0;
        }

        .btn:hover {
            background: #d8a15aff;
        }

        .ventas {
            background: #0d6efd;
        }

        .ventas:hover {
            background: #0b5ed7;
        }

        .bodega {
            background: #198754;
        }

        .bodega:hover {
            background: #157347;
        }

        .produccion {
            background: #fd7e14;
        }

        .produccion:hover {
            background: #fd8c2c;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>TESORO D' MIMI</h2>
        <div class="login-logo">
            <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" alt="Tesoro D' MIMI" class="logo-image">
        </div>

        <h2>Seleccionar Área</h2>
        <p>Elige a qué sección deseas ingresar:</p>

        <!-- Botones que redirigen al inicio pasando un parámetro 'area' -->
        <button class="btn ventas" onclick="seleccionarArea('ventas')">Ventas</button>
        <button class="btn bodega" onclick="seleccionarArea('bodega')">Bodega</button>
        <button class="btn produccion" onclick="seleccionarArea('produccion')" disabled style="opacity: 0.6; cursor: not-allowed;">Producción</button>
        <small class="text-muted d-block mt-2" style="font-style: italic;">Próximamente</small>
    </div>

    <script>
        async function seleccionarArea(area) {
            // Intentar guardar la selección en la sesión del servidor
            try {
                const resp = await fetch('/sistema/public/index.php?route=user&caso=seleccionar-area', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ area: area })
                });

                // Intentar parsear la respuesta aunque falle
                let json = null;
                try { json = await resp.json(); } catch (e) { /* ignore */ }

                if (!resp.ok || (json && json.status && json.status !== 200 && json.status !== '200')) {
                    console.warn('No fue posible guardar la selección en servidor, continuando con almacenamiento local', json);
                }
            } catch (err) {
                console.warn('Error guardando área en servidor:', err);
            }

            // Guardar también en sessionStorage para uso inmediato en la UI
            try { sessionStorage.setItem('selected_area', area); } catch(e) {}

            // Redirigimos a la página específica creada para cada área
            if (area === 'ventas') {
                window.location.href = '/sistema/public/ventas.php';
            } else if (area === 'bodega') {
                window.location.href = '/sistema/public/bodega.php';
            } else if (area === 'produccion') {
                window.location.href = '/sistema/public/produccion.php';
            } else {
                window.location.href = '/sistema/public/inicio';
            }
        }
    </script>
</body>
</html>
