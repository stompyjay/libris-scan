// ==========================================
// CONFIGURACIÓN PARA LARAVEL BLADE (Sesiones)
// ==========================================

const API_URL = '/api'; // Usamos rutas relativas porque estamos en el mismo dominio

// 1. OBTENER EL TOKEN CSRF
// Laravel pone este token en una etiqueta <meta> en el <head> de tus vistas Blade.
// Es OBLIGATORIO para hacer peticiones POST/PUT/DELETE desde JS.
function getCsrfToken() {
    const tokenTag = document.querySelector('meta[name="csrf-token"]');
    return tokenTag ? tokenTag.content : '';
}

// 2. CABECERAS (Ya no usamos Bearer Token)
// Usamos 'X-CSRF-TOKEN' para seguridad y le decimos al navegador
// que envíe las cookies automáticamente.
function getAuthHeaders() {
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
    };
}

// 3. VERIFICAR SESIÓN
// En este modo, si no estás logueado, Laravel te redirige solo desde el backend.
// Pero si quieres comprobarlo en JS:
async function checkAuth() {
    try {
        const response = await fetch('/api/user', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            // Si el servidor dice que no estamos logueados (401), nos vamos al login
            window.location.href = '/login';
        }
    } catch (error) {
        console.error("Error verificando sesión", error);
    }
}

console.log("Modo Híbrido: JS usando Sesión de Laravel");