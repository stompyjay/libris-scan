<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Finalizar Compra - BookStore</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #1a1a1e; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
        *:focus-visible { outline: 2px solid #3b82f6; outline-offset: 2px; }
    </style>
</head>
<body class="bg-[#0f0f12] text-white font-sans min-h-screen flex items-center justify-center">

    <main id="main-content" class="w-full max-w-4xl p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <section class="bg-[#1a1a1e] p-6 rounded-2xl border border-gray-800 h-fit shadow-lg">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-white">
                <i class="fas fa-shopping-bag text-blue-500"></i> Resumen de tu pedido
            </h2>

            <div id="checkout-items" class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                </div>

            <div class="mt-6 pt-6 border-t border-gray-800">
                <div class="flex justify-between items-center text-xl font-bold text-blue-400 mt-4">
                    <span>Total a Pagar</span>
                    <span id="total-price">0.00€</span>
                </div>
            </div>
            
            <a href="{{ url('/dashboard.html') }}" class="block text-center mt-6 text-sm text-gray-400 hover:text-white transition underline">
                <i class="fas fa-arrow-left"></i> Cancelar y volver
            </a>
        </section>

        <section class="bg-[#1a1a1e] p-6 rounded-2xl border border-gray-800 shadow-lg">
            <h2 class="text-xl font-bold mb-6 text-white">Datos de Pago</h2>
            
            <form id="payment-form" onsubmit="confirmarCompra(event)" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-2 uppercase">Nombre en la tarjeta</label>
                    <input type="text" required class="w-full bg-[#121214] border border-gray-700 rounded-lg p-3 text-sm text-white focus:border-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-2 uppercase">Número de tarjeta</label>
                    <div class="relative">
                        <i class="fas fa-credit-card absolute left-3 top-3.5 text-gray-500"></i>
                        <input type="text" required placeholder="0000 0000 0000 0000" maxlength="19"
                            class="w-full bg-[#121214] border border-gray-700 rounded-lg p-3 pl-10 text-sm text-white focus:border-blue-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-2 uppercase">Caducidad</label>
                        <input type="text" required placeholder="MM/YY" maxlength="5" class="w-full bg-[#121214] border border-gray-700 rounded-lg p-3 text-sm text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-2 uppercase">CVV</label>
                        <input type="password" required placeholder="123" maxlength="3" class="w-full bg-[#121214] border border-gray-700 rounded-lg p-3 text-sm text-white">
                    </div>
                </div>

                <button type="submit" id="submit-btn"
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl mt-6 transition shadow-lg shadow-blue-900/20 flex justify-center items-center gap-2">
                    <span>Pagar Ahora</span>
                    <i class="fas fa-lock text-xs opacity-70"></i>
                </button>
            </form>
        </section>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const cart = JSON.parse(localStorage.getItem('carritoParaPagar')) || [];
        
        if (cart.length === 0) {
            window.location.href = "{{ url('/') }}";
            return;
        }

        const container = document.getElementById('checkout-items');
        let total = 0;

        container.innerHTML = cart.map(item => {
            const price = parseFloat(item.price) || 9.95; 
            total += price;
            return `
                <div class="flex gap-4 bg-[#121214] p-3 rounded-lg border border-gray-800 items-center">
                    <img src="${item.cover || 'https://via.placeholder.com/150'}" class="w-12 h-16 object-cover rounded shadow-sm">
                    <div class="flex-1">
                        <h3 class="font-bold text-sm text-gray-200">${item.title}</h3>
                        <p class="text-sm font-bold text-blue-400 mt-1">${price.toFixed(2)}€</p>
                    </div>
                </div>`;
        }).join('');

        const totalFmt = total.toFixed(2) + '€';
        document.getElementById('total-price').innerText = totalFmt;
        document.getElementById('submit-btn').firstElementChild.innerText = `Pagar ${totalFmt}`;
    });

    async function confirmarCompra(e) {
        e.preventDefault();
        const btn = document.getElementById('submit-btn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        btn.disabled = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const cart = JSON.parse(localStorage.getItem('carritoParaPagar')) || [];

        try {
            // Asegúrate de tener Route::post('/purchase', ...) en web.php
            const response = await fetch("{{ url('/purchase') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ books: cart })
            });

            if (!response.ok) throw new Error('Error en el servidor');

            localStorage.removeItem('carritoParaPagar');
            alert("¡Compra realizada con éxito!");
            window.location.href = "{{ url('/books') }}"; // Redirige a Mis Libros

        } catch (error) {
            console.error(error);
            alert("Hubo un error al procesar la compra.");
            btn.disabled = false;
            btn.innerText = "Reintentar";
        }
    }
    </script>
</body>
</html>