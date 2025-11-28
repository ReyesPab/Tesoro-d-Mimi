$(document).ready(function() {
    // Validación del formulario
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
    })();

    // Envío del formulario
    $("#registroResultado").submit(function(event) {
        event.preventDefault();
        
        // Obtener valores del formulario
        let formData = {
            ID_Paciente: $("#id_paciente").val(),
            ID_Laboratorio: $("#id_laboratorio").val(),
            Tipo_Examen: $("#tipo_examen").val(),
            Resultado: $("#resultado").val(),
            Fecha_Resultado: $("#fecha_resultado").val()
        };

        // Validación adicional
        if (!formData.ID_Paciente || !formData.ID_Laboratorio || !formData.Tipo_Examen || !formData.Resultado) {
            $("#mensaje").html("<div class='alert alert-danger'>Todos los campos obligatorios deben ser completados</div>").show();
            return;
        }

        $.ajax({
            url: "/gestion/public/resultadoexamen/",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.error("Error parsing JSON:", e);
                        $("#mensaje").html("<div class='alert alert-danger'>Error al procesar la respuesta del servidor</div>").show();
                        return;
                    }
                }
                
                if(response && response.status === 201) {
                    $("#mensaje").html("<div class='alert alert-success'>"+ (response.message || "Resultado de examen creado con éxito") +"</div>").show();
                    $("#registroResultado")[0].reset();
                    $("#registroResultado").removeClass('was-validated');
                    
                    // Redirigir después de 2 segundos
                    setTimeout(function() {
                        window.location.href = "resultados_examenes.php";
                    }, 2000);
                } else {
                    $("#mensaje").html("<div class='alert alert-danger'>"+ (response.message || "Error al crear el resultado de examen") +"</div>").show();
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = "Error en el servidor";
                try {
                    const response = xhr.responseText ? JSON.parse(xhr.responseText) : null;
                    errorMsg = (response && response.message) ? response.message : 
                              (xhr.responseText || error || "Error desconocido");
                } catch (e) {
                    console.error("Error parsing error response:", e);
                    errorMsg = xhr.responseText || error || "Error en el servidor";
                }
                $("#mensaje").html("<div class='alert alert-danger'>"+errorMsg+"</div>").show();
            }
        });
    });

    // Autocompletar datos del paciente (opcional)
    $("#id_paciente").change(function() {
        const idPaciente = $(this).val();
        if(idPaciente) {
            $.get(`/gestion/public/paciente/${idPaciente}`, function(data) {
                if(data && data.ID_Paciente) {
                    // Puedes mostrar información del paciente si lo deseas
                    console.log("Paciente encontrado:", data);
                }
            }).fail(function() {
                console.log("Paciente no encontrado");
                $("#mensaje").html("<div class='alert alert-warning'>El ID de paciente no existe</div>").show().delay(3000).fadeOut();
            });
        }
    });

    // Autocompletar datos del laboratorio (opcional)
    $("#id_laboratorio").change(function() {
        const idLaboratorio = $(this).val();
        if(idLaboratorio) {
            $.get(`/gestion/public/laboratorio/${idLaboratorio}`, function(data) {
                if(data && data.ID_Laboratorio) {
                    // Puedes mostrar información del laboratorio si lo deseas
                    console.log("Laboratorio encontrado:", data);
                }
            }).fail(function() {
                console.log("Laboratorio no encontrado");
                $("#mensaje").html("<div class='alert alert-warning'>El ID de laboratorio no existe</div>").show().delay(3000).fadeOut();
            });
        }
    });
});