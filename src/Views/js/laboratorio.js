$(document).ready(function() {
    // Validación de teléfono (solo números)
    $('#telefono').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $('#laboratorioForm').submit(function(e) {
        e.preventDefault();
        
        // Resetear validación
        $('.is-invalid').removeClass('is-invalid');
        let isValid = true;
        
        // Validar campos requeridos
        $('#laboratorioForm [required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            }
        });
        
        // Validar formato de correo
        const email = $('#correo').val();
        if (email && !/^\S+@\S+\.\S+$/.test(email)) {
            $('#correo').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            Swal.fire('Error', 'Complete todos los campos requeridos correctamente', 'error');
            return;
        }

        // Mostrar carga
        Swal.fire({
            title: 'Procesando...',
            html: 'Registrando laboratorio',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Preparar datos
        const formData = {
            Nombre: $('#nombre').val(),
            Direccion: $('#direccion').val(),
            Telefono: $('#telefono').val(),
            Correo: $('#correo').val()
        };

        console.log("Datos a enviar:", formData);
        
        // Enviar datos
        $.ajax({
            url: '/gestion/public/lab/', // Asegúrate que esta ruta es correcta
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                Swal.close();
                
                if (response.status === 'success' || response.status === 201) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: response.message || 'Laboratorio registrado correctamente',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = '/gestion/public/laboratorios'; // Redirigir a lista
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Error al registrar laboratorio',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                let errorMsg = "Error en la comunicación con el servidor";
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                    
                    // Mostrar errores de validación del servidor
                    if (response.errors) {
                        errorMsg = Object.values(response.errors).join('<br>');
                    }
                } catch(e) {
                    console.error("Error parseando respuesta:", e);
                }
                
                Swal.fire({
                    title: "Error!",
                    html: errorMsg,
                    icon: "error"
                });
            }
        });
    });
});