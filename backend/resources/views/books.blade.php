<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tablero de Libros - Drag & Drop</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <link href="{{ asset('dflip/css/dflip.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('dflip/css/themify-icons.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('dflip/js/dflip.min.js') }}" type="text/javascript"></script>
    
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1a1a1e; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
        
        .drop-zone { transition: all 0.3s ease; }
        .drag-over {
            background-color: rgba(37, 99, 235, 0.1) !important;
            border-color: #3b82f6 !important;
            transform: scale(1.01);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
        }
        #completed-list.drag-over {
            background-color: rgba(16, 185, 129, 0.1) !important;
            border-color: #10b981 !important;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
        }
        .dragging { opacity: 0.5; cursor: grabbing; }
        .book-card { cursor: grab; }
        .book-card:active { cursor: grabbing; }
    </style>
</head>

<body class="bg-[#0f0f12] text-gray-300 font-sans min-h-screen flex flex-col">

    <nav class="bg-[#1a1a1e] border-b border-gray-800 p-4 sticky top-0 z-40 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-columns text-blue-500"></i> Mi Tablero de Lectura
            </h1>
            <a href="{{ url('/dashboard.html') }}" class="text-sm text-gray-400 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 h-full">

            <div class="flex flex-col h-full">
                <div class="bg-blue-900/20 border-t-4 border-blue-500 p-3 rounded-t-lg flex justify-between items-center">
                    <h2 class="font-bold text-blue-100 uppercase tracking-wider text-sm">
                        <i class="fas fa-hourglass-half mr-2"></i> Por Leer / Leyendo
                    </h2>
                    <span id="count-pending" class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full font-mono">
                        {{ $books->where('status', '!=', 'completed')->count() }}
                    </span>
                </div>
                
                <div id="pending-list" 
                     class="drop-zone flex-grow bg-[#18181b] border-2 border-dashed border-gray-700 rounded-b-lg p-4 space-y-3 min-h-[500px]"
                     ondragover="allowDrop(event)"
                     ondragleave="removeDragStyle(event)"
                     ondrop="handleDrop(event, 'pending')">
                     
                     @forelse($books->where('status', '!=', 'completed') as $book)
                        <div class="book-card bg-white p-4 rounded-lg shadow-md mb-4 border-l-4 border-blue-500"
                             draggable="true"
                             data-id="{{ $book->id }}"
                             ondragstart="dragStart(event, '{{ $book->id }}')">
                            
                            <div class="flex justify-between items-start">
                                <h3 class="font-bold text-lg text-gray-800">{{ $book->title }}</h3>
                                <span class="text-xs font-semibold px-2 py-1 rounded bg-blue-100 text-blue-800">
                                    {{ $book->status ?? 'Pendiente' }}
                                </span>
                            </div>
                            
                            <p class="text-gray-600 text-sm mt-1">
                                @if($book->authors && $book->authors->count() > 0)
                                    {{ $book->authors->pluck('name')->join(', ') }}
                                @else
                                    Autor desconocido
                                @endif
                            </p>

                            <div class="mt-3 flex justify-end gap-2">
                                <button onclick="openReader('{{ $book->title }}', 'path/to/pdf.pdf')" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                    <i class="fas fa-book-open"></i> Leer
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 mt-10 italic">No tienes libros pendientes.</p>
                     @endforelse
                </div>
            </div>

            <div class="flex flex-col h-full">
                <div class="bg-green-900/20 border-t-4 border-green-500 p-3 rounded-t-lg flex justify-between items-center">
                    <h2 class="font-bold text-green-100 uppercase tracking-wider text-sm">
                        <i class="fas fa-check-circle mr-2"></i> Terminados
                    </h2>
                    <span id="count-completed" class="bg-green-600 text-white text-xs px-2 py-1 rounded-full font-mono">
                        {{ $books->where('status', 'completed')->count() }}
                    </span>
                </div>

                <div id="completed-list" 
                     class="drop-zone flex-grow bg-[#18181b] border-2 border-dashed border-gray-700 rounded-b-lg p-4 space-y-3 min-h-[500px]"
                     ondragover="allowDrop(event)"
                     ondragleave="removeDragStyle(event)"
                     ondrop="handleDrop(event, 'completed')">
                     
                     @foreach($books->where('status', 'completed') as $book)
                        <div class="book-card bg-white p-4 rounded-lg shadow-md mb-4 border-l-4 border-green-500"
                             draggable="true"
                             data-id="{{ $book->id }}"
                             ondragstart="dragStart(event, '{{ $book->id }}')">
                            
                            <div class="flex justify-between items-start">
                                <h3 class="font-bold text-lg text-gray-800">{{ $book->title }}</h3>
                                <span class="text-xs font-semibold px-2 py-1 rounded bg-green-100 text-green-800">
                                    Completado
                                </span>
                            </div>
                            
                            <p class="text-gray-600 text-sm mt-1">
                                @if($book->authors && $book->authors->count() > 0)
                                    {{ $book->authors->pluck('name')->join(', ') }}
                                @else
                                    Autor desconocido
                                @endif
                            </p>

                            <div class="mt-3 flex justify-end gap-2">
                                <button class="text-gray-500 cursor-not-allowed text-sm font-medium" disabled>
                                    <i class="fas fa-check"></i> Leído
                                </button>
                            </div>
                        </div>
                        @endforeach
                </div>
            </div>

        </div>

        <div id="reader-modal" class="fixed inset-0 z-50 hidden bg-[#0f0f12] flex-col">
            <div class="h-12 bg-[#1a1a1e] border-b border-gray-800 flex justify-between items-center px-4 shadow-lg z-10">
                <h3 id="reader-title" class="text-white font-bold text-sm truncate w-1/2">Leyendo...</h3>
                <button onclick="closeReader()" class="text-gray-400 hover:text-white transition px-3 py-1 bg-red-900/30 hover:bg-red-900/50 rounded text-xs border border-red-900 cursor-pointer">
                    <i class="fas fa-times mr-1"></i> Cerrar Libro
                </button>
            </div>
            <div class="flex-grow relative w-full h-full bg-[#0f0f12]">
                <div id="flipbook-container" class="absolute inset-0 w-full h-full"></div>
            </div>
        </div>
    </main>

    <script>
    // Configuración dFlip
    var dFlipLocation = "{{ asset('dflip') }}/";

    // --- 1. LÓGICA DRAG & DROP ---
    function dragStart(event, bookId) {
        // Guardamos el ID del libro
        event.dataTransfer.setData("text/plain", bookId);
        event.dataTransfer.effectAllowed = "move";
        event.target.classList.add('dragging');
    }

    function dragEnd(event) {
        event.target.classList.remove('dragging');
    }

    function allowDrop(event) {
        event.preventDefault(); // Necesario para permitir soltar
        event.currentTarget.classList.add('drag-over');
    }

    function removeDragStyle(event) {
        event.currentTarget.classList.remove('drag-over');
    }

    async function handleDrop(event, newStatus) {
        event.preventDefault();
        event.currentTarget.classList.remove('drag-over');
        
        const bookId = event.dataTransfer.getData("text/plain");
        if (!bookId) return;

        // Buscar la tarjeta por su atributo data-id
        const card = document.querySelector(`.book-card[data-id='${bookId}']`);
        const destination = newStatus === 'pending' ? document.getElementById('pending-list') : document.getElementById('completed-list');
        
        // Mover visualmente
        if(card && destination) {
            destination.appendChild(card);
            
            // Actualizar estilos visuales según columna (opcional, cambio de borde)
            if(newStatus === 'completed') {
                card.classList.remove('border-blue-500');
                card.classList.add('border-green-500');
                // Actualizar badge
                const badge = card.querySelector('span');
                if(badge) {
                    badge.className = 'text-xs font-semibold px-2 py-1 rounded bg-green-100 text-green-800';
                    badge.innerText = 'Completado';
                }
            } else {
                card.classList.remove('border-green-500');
                card.classList.add('border-blue-500');
                // Actualizar badge
                const badge = card.querySelector('span');
                if(badge) {
                    badge.className = 'text-xs font-semibold px-2 py-1 rounded bg-blue-100 text-blue-800';
                    badge.innerText = 'Pendiente';
                }
            }
            updateCounters();
        }

        // Llamada API
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            // Asegúrate de que esta ruta coincida con tu api.php o web.php
            // Ejemplo: Route::patch('/books/{book}/status', ...)
            await fetch(`/api/books/${bookId}/status`, { 
                method: 'POST', // A veces Laravel prefiere POST con _method: PATCH
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ 
                    status: newStatus,
                    _method: 'PATCH' // Truco para asegurar compatibilidad
                })
            });
            console.log('Estado actualizado a:', newStatus);
        } catch (error) { 
            console.error('Error al actualizar:', error); 
        }
    }

    // --- 2. LÓGICA LECTOR ---
    function openReader(title, pdfUrl) {
        const modal = document.getElementById('reader-modal');
        const container = document.getElementById('flipbook-container');
        document.getElementById('reader-title').innerText = `Leyendo: ${title}`;
        container.innerHTML = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        $(container).flipBook(pdfUrl, {
            height: '100%',
            duration: 800,
            backgroundColor: "#0f0f12"
        });
    }

    function closeReader() {
        const modal = document.getElementById('reader-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('flipbook-container').innerHTML = '';
    }

    function updateCounters() {
        document.getElementById('count-pending').innerText = document.getElementById('pending-list').children.length;
        document.getElementById('count-completed').innerText = document.getElementById('completed-list').children.length;
    }
    </script>
</body>
</html>