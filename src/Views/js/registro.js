$(document).ready(function() {
    // Limpiar validaciones al cambiar campos
    $('input, select').on('input change', function() {
        $(this).removeClass('is-invalid');
        if($(this).attr('id') === 'confirmar_contrasena' && $(this).val() === $('#contrasena').val()) {
            $(this).removeClass('is-invalid');
        }
    });

    $("#form_register").submit(function(e) {
        e.preventDefault();
        
        // Resetear validaciones
        $('.is-invalid').removeClass('is-invalid');
        let isValid = true;

        // Validar usuario (máximo 15 caracteres)
        if ($('#usuario').val().length > 15) {
            $('#usuario').addClass('is-invalid');
            Swal.fire({
                title: "Error!",
                text: "El usuario no puede tener más de 15 caracteres",
                icon: "error"
            });
            isValid = false;
        }

        // Validar contraseñas coincidan
        if ($('#contrasena').val() !== $('#confirmar_contrasena').val()) {
            $('#confirmar_contrasena').addClass('is-invalid');
            Swal.fire({
                title: "Error!",
                text: "Las contraseñas no coinciden",
                icon: "error"
            });
            isValid = false;
        }

        // Validar correo electrónico
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($('#correo_electronico').val())) {
            $('#correo_electronico').addClass('is-invalid');
            Swal.fire({
                title: "Error!",
                text: "El correo electrónico no tiene un formato válido",
                icon: "error"
            });
            isValid = false;
        }

        // Validar Id_Rol (debe ser numérico)
        if (!/^\d+$/.test($('#id_rol').val())) {
            $('#id_rol').addClass('is-invalid');
            Swal.fire({
                title: "Error!",
                text: "El rol debe ser un número válido",
                icon: "error"
            });
            isValid = false;
        }

        if (!isValid) return false;

        // Crear objeto con los datos CORREGIDOS para tu BD
        const formData = {
            Usuario: $('#usuario').val(),
            Nombre_Usuario: $('#nombre_usuario').val(),
            Correo_Electronico: $('#correo_electronico').val(),
            Contraseña: $('#contrasena').val(),
            Id_Rol: $('#id_rol').val(),
            Estado_Usuario: 'Activo',
            Numero_Identidad: $('#numero_identidad').val() || null
        };

        // Depuración
        console.log("Datos a enviar:", formData);

        $.ajax({
            url: '/rosquilla/public/user/',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                console.log("Respuesta:", response);
                
                if (response.status === 201) {
                    Swal.fire({
                        title: "¡Registro exitoso!",
                        text: response.message || "El usuario ha sido registrado correctamente",
                        icon: "success",
                        confirmButtonText: "Aceptar"
                    }).then(() => {
                        window.location.href = "/rosquilla/public/login";
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message || "Error al registrar el usuario",
                        icon: "error"
                    });
                }
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                let errorMsg = "Error al registrar el usuario";
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch(e) {
                    console.error("Error parseando respuesta:", e);
                }
                Swal.fire({
                    title: "Error!",
                    text: errorMsg,
                    icon: "error"
                });
            }
        });
    });

    // Ocultar campos que no existen en tu BD
    $('.doctor-fields').hide();
});