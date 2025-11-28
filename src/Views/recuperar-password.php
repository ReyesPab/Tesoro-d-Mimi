<!DOCTYPE html>
<html>
<head>
    <title>Recuperación de Contraseña - Sistema Rosquilleria</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 { 
            color: #007bff; 
            text-align: center;
            margin-bottom: 30px;
        }
        input, button { 
            padding: 12px; 
            margin: 5px; 
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        input {
            width: 200px;
            text-transform: uppercase;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
        .result { 
            background: #f8f9fa; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        .loading { border-left-color: #ffc107; background: #fff3cd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Recuperación de Contraseña - Sistema Rosquilleria</h1>
        
        <div style="text-align: center; margin-bottom: 30px;">
            <input type="text" id="USUARIO" placeholder="Ingrese usuario" style="text-transform: uppercase">
            <button onclick="probarRecuperacion()">Probar Recuperación</button>
        </div>
        
        <div id="result" class="result"></div>

        <div class="links">
        <a href="/sistema/public/login">Volver al login</a>
    </div>
    </div>

    

    <script>
        async function probarRecuperacion() {
            const usuario = document.getElementById('USUARIO').value.trim().toUpperCase();
            const resultDiv = document.getElementById('result');
            
            if (!usuario) {
                resultDiv.innerHTML = '<div class="error">Por favor ingrese un usuario</div>';
                return;
            }
            
            resultDiv.innerHTML = '<div class="loading">Procesando solicitud de recuperación...</div>';
            
            try {
                const response = await fetch('/sistema/public/index.php?route=auth&caso=recuperar-password-avanzado', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ 
                        usuario: usuario,
                        metodo: 'correo'
                    })
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    resultDiv.innerHTML = `
                        <div class="success">
                            <h3>${data.message}</h3>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `<div class="error">${data.message}</div>`;
                }
                
            } catch (error) {
                resultDiv.innerHTML = `<div class="error">Error de conexión: ${error.message}</div>`;
            }
        }
    </script>
</body>
</html>