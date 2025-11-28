// Función para generar el menú dinámico basado en permisos
function generarMenu() {
    // Obtener datos del usuario del localStorage
    const userData = JSON.parse(localStorage.getItem('userData'));
    
    // Verificar si hay datos de usuario
    if (!userData || !userData.permisos) {
        console.error('Datos de usuario no encontrados');
        return [
            { path: '/login', label: 'Iniciar Sesión' }
        ];
    }

    const menuItems = [];
    
    // Items comunes para todos los usuarios autenticados
    menuItems.push({ 
        path: '/perfil', 
        label: 'Mi Perfil',
        icon: 'fa-user' // Ejemplo con icono de FontAwesome
    });
    
    // Items basados en permisos
    if (userData.permisos.includes('gestion_usuarios')) {
        menuItems.push({ 
            path: '/usuarios', 
            label: 'Gestión de Usuarios',
            icon: 'fa-users'
        });
    }
    
    if (userData.permisos.includes('gestion_pacientes')) {
        menuItems.push({ 
            path: '/pacientes', 
            label: 'Pacientes',
            icon: 'fa-procedures'
        });
    }
    
    if (userData.permisos.includes('gestion_citas')) {
        menuItems.push({ 
            path: '/citas', 
            label: 'Gestión de Citas',
            icon: 'fa-calendar-check'
        });
    }
    
    if (userData.permisos.includes('ver_reportes')) {
        menuItems.push({ 
            path: '/reportes', 
            label: 'Reportes',
            icon: 'fa-chart-bar'
        });
    }
    
    if (userData.permisos.includes('gestion_configuracion')) {
        menuItems.push({ 
            path: '/configuracion', 
            label: 'Configuración',
            icon: 'fa-cog'
        });
    }

    // Item de cierre de sesión
    menuItems.push({
        path: '#',
        label: 'Cerrar Sesión',
        icon: 'fa-sign-out-alt',
        onClick: 'logout()'
    });
    
    return menuItems;
}

// Función para renderizar el menú en el navbar
function renderizarMenu() {
    const menuItems = generarMenu();
    const menuContainer = document.getElementById('main-menu');
    
    if (!menuContainer) return;
    
    menuContainer.innerHTML = '';
    
    menuItems.forEach(item => {
        const menuItem = document.createElement('li');
        menuItem.className = 'nav-item';
        
        const link = document.createElement('a');
        link.className = 'nav-link';
        link.href = item.path;
        link.innerHTML = `<i class="fas ${item.icon || 'fa-circle'}"></i> ${item.label}`;
        
        if (item.onClick) {
            link.setAttribute('onclick', item.onClick);
        }
        
        menuItem.appendChild(link);
        menuContainer.appendChild(menuItem);
    });
}

// Llamar a renderizarMenu cuando la página cargue
document.addEventListener('DOMContentLoaded', function() {
    renderizarMenu();
    
    // También puedes llamarlo cuando cambie el estado de autenticación
});

// Función de logout
function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('userData');
    window.location.href = '/login';
}