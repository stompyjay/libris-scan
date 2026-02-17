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
        /* Focus visible para accesibilidad */
        *:focus-visible { outline: 2px solid #60a5fa; outline-offset: 2px; }
    </style>
</head>
<body class="bg-[#0f0f12] text-gray-200 font-sans min-h-screen flex items-center justify-center">

    <main id="main-content" class="w-full max-w-4xl p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <section class="bg-[#1a1a1e] p-6 rounded-2xl border border-gray-700 h-fit shadow-lg" aria-labelledby="summary-title">
            <h2 id="summary-title" class="text-xl font-bold mb-6 flex items-center gap-2 text-white">
                <i class="fas fa-shopping-bag text-blue-400" aria-hidden="true"></i> Resumen de tu pedido
            </h2>

            <div id="checkout-items" class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar" role="list">
                </div>

            <div class="mt-6 pt-6 border-t border-gray-700">
                <div class="flex justify-between items-center text-xl font-bold text-blue-400 mt-4">
                    <span>Total a Pagar</span>
                    <span id="total-price">0.00€</span>
                </div>
            </div>
            
            <a href="{{ url('/dashboard.html') }}" class="block text-center mt-6 text-sm text-gray-300 hover:text-white transition underline focus:text-white">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Cancelar y volver
            </a>
        </section>

        <section class="bg-[#1a1a1e] p-6 rounded-2xl border border-gray-700 shadow-lg" aria-labelledby="payment-title">
            <h2 id="payment-title" class="text-xl font-bold mb-6 text-white">Datos de Pago</h2>
            
            <form id="payment-form" onsubmit="confirmarCompra(event)" class="space-y-5">
                
                <div>
                    <label for="card-name" class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Nombre en la tarjeta</label>
                    <input type="text" id="card-name" name="card-name" required 
                           class="w-full bg-[#121214] border border-gray-600 rounded-lg p-3 text-sm text-white focus:border-blue-500 outline-none placeholder-gray-600 transition focus:ring-1 focus:ring-blue-500"
                           placeholder="Ej: Juan Pérez">
                </div>

                <div>
                    <label for="card-number" class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Número de tarjeta</label>
                    <div class="relative">
                        <i class="fas fa-credit-card absolute left-3 top-3.5 text-gray-500" aria-hidden="true"></i>
                        <input type="text" id="card-number" name="card-number" required placeholder="0000 0000 0000 0000" maxlength="19"
                            class="w-full bg-[#121214] border border-gray-600 rounded-lg p-3 pl-10 text-sm text-white focus:border-blue-500 outline-none placeholder-gray-600 transition focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="expiry" class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Caducidad</label>
                        <input type="text" id="expiry" name="expiry" required placeholder="MM/YY" maxlength="5" 
                               class="w-full bg-[#121214] border border-gray-600 rounded-lg p-3 text-sm text-white focus:border-blue-500 outline-none placeholder-gray-600 transition focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="cvv" class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">CVV</label>
                        <input type="password" id="cvv" name="cvv" required placeholder="123" maxlength="3" 
                               class="w-full bg-[#121214] border border-gray-600 rounded-lg p-3 text-sm text-white focus:border-blue-500 outline-none placeholder-gray-600 transition focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <button type="submit" id="submit-btn"
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl mt-6 transition shadow-lg shadow-blue-900/20 flex justify-center items-center gap-2 focus:ring-4 focus:ring-blue-500/50">
                    <span>Pagar Ahora</span>
                    <i class="fas fa-lock text-xs opacity-70" aria-hidden="true"></i>
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
            // ACCESIBILIDAD: Añadido alt descriptivo dinámico
            return `
                <article class="flex gap-4 bg-[#121214] p-3 rounded-lg border border-gray-700 items-center" role="listitem">
                    <img src="${item.cover || 'https://via.placeholder.com/150'}" alt="Portada de ${item.title}" class="w-12 h-16 object-cover rounded shadow-sm border border-gray-600">
                    <div class="flex-1">
                        <h3 class="font-bold text-sm text-white">${item.title}</h3>
                        <p class="text-sm font-bold text-blue-400 mt-1">${price.toFixed(2)}€</p>
                    </div>
                </article>`;
        }).join('');

        const totalFmt = total.toFixed(2) + '€';
        document.getElementById('total-price').innerText = totalFmt;
        // Solo actualizamos el texto del span, no borramos el icono
        document.querySelector('#submit-btn span').innerText = `Pagar ${totalFmt}`;
    });

    async function confirmarCompra(e) {
        e.preventDefault();
        const btn = document.getElementById('submit-btn');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Procesando...';
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const cart = JSON.parse(localStorage.getItem('carritoParaPagar')) || [];

        try {
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
            // Usar un div de alerta accesible sería mejor que un alert nativo, pero por ahora:
            alert("¡Compra realizada con éxito!");
            window.location.href = "{{ url('/books') }}"; 

        } catch (error) {
            console.error(error);
            alert("Hubo un error al procesar la compra.");
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = "Reintentar";
        }
    }
    </script>
</body>
</html>