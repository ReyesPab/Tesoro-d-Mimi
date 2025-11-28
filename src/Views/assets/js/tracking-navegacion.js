class TrackingNavegacion {
    constructor() {
        this.paginaActual = this.obtenerNombrePagina();
        this.registrarAcceso();
    }

    obtenerNombrePagina() {
        const path = window.location.pathname;
        const urlParams = new URLSearchParams(window.location.search);
        const route = urlParams.get('route');
        
        if (route) {
            return route;
        }
        
        // Extraer nombre de página del path
        const match = path.match(/\/([^\/]+)\.php$/);
        if (match) {
            return match[1];
        }
        
        // Para rutas amigables
        const segments = path.split('/').filter(segment => segment);
        return segments[segments.length - 1] || 'inicio';
    }

    async registrarAcceso() {
        try {
            // Verificar si el usuario está logueado
            const usuarioLogueado = this.verificarSesion();
            
            if (!usuarioLogueado) {
                console.log('Usuario no logueado, no se registra navegación');
                return;
            }

            const response = await fetch('index.php?route=bitacora&caso=registrar-navegacion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    pagina: this.paginaActual,
                    accion: 'NAVEGACION'
                })
            });

            if (response.ok) {
                console.log(`✅ Navegación registrada: ${this.paginaActual}`);
            } else {
                console.error('❌ Error al registrar navegación');
            }
        } catch (error) {
            console.error('❌ Error en tracking:', error);
        }
    }

    verificarSesion() {
        // Verificar si hay una sesión activa
        // Puedes ajustar esto según tu sistema de autenticación
        return document.cookie.includes('PHPSESSID') || 
               localStorage.getItem('user_session') || 
               sessionStorage.getItem('user_session');
    }

    // Método para registrar acciones específicas
    static registrarAccionEspecifica(accion, descripcion, idObjeto = null) {
        try {
            fetch('index.php?route=bitacora&caso=registrar-navegacion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    accion: accion,
                    descripcion: descripcion,
                    id_objeto: idObjeto
                })
            });
        } catch (error) {
            console.error('Error registrando acción:', error);
        }
    }
}

// Inicializar tracking cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    new TrackingNavegacion();
});