$("#form_login").submit(function () {
    let correo = $("#correo").val();
    let contrasena = $("#contrasena").val();
    
    if(correo != "" && contrasena != ""){
        $.ajax({
            url: '/rosquilla/public/user/login/' + correo + '/' + contrasena,
            type: 'get',
            success: function (resp) {
                // Parsear la respuesta JSON
                let response = typeof resp === 'string' ? JSON.parse(resp) : resp;
                
                if (response.status === 200 && response.data) {
                    Swal.fire({
                        title: "¡Bienvenido!",
                        text: "Inicio de sesión exitoso",
                        icon: "success"
                    }).then(() => {
                        // Redirigir al menú después de un inicio de sesión exitoso
                        window.location.href = "menu";
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message || "Credenciales incorrectas",
                        icon: "error"
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    title: "Error!",
                    text: "Hubo un problema al iniciar sesión. Por favor, inténtalo de nuevo.",
                    icon: "error"
                });
            }
        });
    } else {
        Swal.fire({
            title: "Debes llenar todos los campos!",
            text: " ",
            icon: "warning"
        });
    }
    return false;
});