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
    $("#registroInforme").submit(function(event) {
        event.preventDefault();
        
        // Obtener valores del formulario
        let formData = {
            ID_Paciente: $("#id_paciente").val(),
            ID_Medico: $("#id_medico").val(),
            Detalles: $("#detalles").val()
        };

        // Validación adicional
        if (!formData.ID_Paciente || !formData.ID_Medico || !formData.Detalles) {
            $("#mensaje").html("<div class='alert alert-danger'>Todos los campos obligatorios deben ser completados</div>").show();
            return;
        }

        $.ajax({
            url: "/gestion/public/informemedico/",
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
                    $("#mensaje").html("<div class='alert alert-success'>"+ (response.message || "Informe médico creado con éxito") +"</div>").show();
                    $("#registroInforme")[0].reset();
                    $("#registroInforme").removeClass('was-validated');
                    
                    // Redirigir después de 2 segundos
                    setTimeout(function() {
                        window.location.href = "informes_medicos.php";
                    }, 2000);
                } else {
                    $("#mensaje").html("<div class='alert alert-danger'>"+ (response.message || "Error al crear el informe médico") +"</div>").show();
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
            });
        }
    });

    // Autocompletar datos del médico (opcional)
    $("#id_medico").change(function() {
        const idMedico = $(this).val();
        if(idMedico) {
            $.get(`/gestion/public/medico/${idMedico}`, function(data) {
                if(data && data.ID_Medico) {
                    // Puedes mostrar información del médico si lo deseas
                    console.log("Médico encontrado:", data);
                }
            }).fail(function() {
                console.log("Médico no encontrado");
            });
        }
    });
});