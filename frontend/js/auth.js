// ==========================================
// CONFIGURACIÓN CENTRAL (frontend/js/auth.js)
// ==========================================

// 1. LA DIRECCIÓN DE TU LARAVEL
// Si usas "php artisan serve", suele ser el puerto 8000.
// Si usas XAMPP, cámbialo a: 'http://localhost/nombre-tu-proyecto/public/api'
const API_URL = 'http://localhost:8000/api'; 

// 2. EL GUARDAESPALDAS (Verificar Sesión)
// Esta función se pone al principio de dashboard, books, etc.
// Si no encuentra el token, te patea fuera al Login.
function checkAuth() {
    const token = localStorage.getItem('auth_token');
    
    // Si no hay "carnet" (token), mandamos al usuario a la puerta (index.html)
    if (!token) {
        window.location.href = '/'; 
        return null; // Detenemos ejecución
    }
    return token;
}

// 3. EL TRADUCTOR (Cabeceras para Fetch)
// Para no escribir esto 100 veces en cada archivo.
// Prepara los datos para que Laravel los entienda y adjunta tu Token.
function getAuthHeaders() {
    const token = localStorage.getItem('auth_token');
    return {
        'Content-Type': 'application/json',  // Enviamos JSON
        'Accept': 'application/json',        // Queremos recibir JSON
        'Authorization': `Bearer ${token}`   // Aquí va tu "carnet" de identidad
    };
}

// 4. SALIR (Logout)
// Borra el carnet del bolsillo y te manda a la entrada.
function logout() {
    localStorage.removeItem('auth_token');
    window.location.href = '/';
}

// 5. AYUDA VISUAL (Opcional)
// Si quieres ver en la consola si el archivo cargó bien
console.log("Sistema de autenticación cargado correctamente.");