<!DOCTYPE html>
<html lang="es"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tablero de Lectura - Mis Libros</title>
    
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
        
        /* Focus visible para navegación por teclado */
        *:focus-visible { outline: 2px solid #60a5fa; outline-offset: 2px; }
    </style>
</head>

<body class="bg-[#0f0f12] text-gray-200 font-sans min-h-screen flex flex-col">

    <nav class="bg-[#1a1a1e] border-b border-gray-800 p-4 sticky top-0 z-40 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-columns text-blue-400" aria-hidden="true"></i> Mi Tablero de Lectura
            </h1>
            <a href="{{ url('/dashboard.html') }}" class="text-sm text-gray-300 hover:text-white transition flex items-center gap-2">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al Dashboard
            </a>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 h-full">

            <div class="flex flex-col h-full">
                <div class="bg-blue-900/20 border-t-4 border-blue-500 p-3 rounded-t-lg flex justify-between items-center">
                    <h2 class="font-bold text-blue-100 uppercase tracking-wider text-sm flex items-center">
                        <i class="fas fa-hourglass-half mr-2" aria-hidden="true"></i> Por Leer / Leyendo
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
                        <article class="book-card bg-white p-3 rounded-lg shadow-md mb-4 border-l-4 border-blue-500 flex gap-4"
                             draggable="true"
                             data-id="{{ $book->id }}"
                             ondragstart="dragStart(event, '{{ $book->id }}')"
                             tabindex="0"
                             aria-label="Libro: {{ $book->title }}">
                            
                            <div class="flex-shrink-0">
                                <img src="{{ $book->cover ?? 'https://via.placeholder.com/100x150?text=No+Cover' }}" 
                                     alt="Portada de {{ $book->title }}" 
                                     class="w-20 h-28 object-cover rounded shadow-sm">
                            </div>

                            <div class="flex-grow flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-1">
                                        <h3 class="font-bold text-base text-gray-900 leading-tight">{{ $book->title }}</h3>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-800 uppercase tracking-wide">
                                            {{ $book->pivot->status ?? 'Pendiente' }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 text-xs mb-2">
                                        @if($book->authors && $book->authors->count() > 0)
                                            {{ $book->authors->pluck('name')->join(', ') }}
                                        @else
                                            Autor desconocido
                                        @endif
                                    </p>
                                </div>

                                <div class="flex justify-end gap-2 mt-auto">
                                    <button onclick="openReader('{{ $book->title }}', 'path/to/pdf.pdf')" 
                                            class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center gap-1 transition p-1 rounded hover:bg-blue-50"
                                            aria-label="Leer {{ $book->title }}">
                                        <i class="fas fa-book-open" aria-hidden="true"></i> Leer
                                    </button>
                                </div>
                            </div>
                        </article>
                     @empty
                        <p class="text-center text-gray-500 mt-10 italic">Arrastra libros aquí para leerlos.</p>
                     @endforelse
                </div>
            </div>

            <div class="flex flex-col h-full">
                <div class="bg-green-900/20 border-t-4 border-green-500 p-3 rounded-t-lg flex justify-between items-center">
                    <h2 class="font-bold text-green-100 uppercase tracking-wider text-sm flex items-center">
                        <i class="fas fa-check-circle mr-2" aria-hidden="true"></i> Terminados
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
                        <article class="book-card bg-white p-3 rounded-lg shadow-md mb-4 border-l-4 border-green-500 flex gap-4 opacity-75 hover:opacity-100 transition"
                             draggable="true"
                             data-id="{{ $book->id }}"
                             ondragstart="dragStart(event, '{{ $book->id }}')"
                             tabindex="0"
                             aria-label="Libro completado: {{ $book->title }}">
                            
                            <div class="flex-shrink-0">
                                <img src="{{ $book->cover ?? 'https://via.placeholder.com/100x150?text=No+Cover' }}" 
                                     alt="Portada de {{ $book->title }}" 
                                     class="w-20 h-28 object-cover rounded shadow-sm grayscale hover:grayscale-0 transition">
                            </div>

                            <div class="flex-grow flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-1">
                                        <h3 class="font-bold text-base text-gray-900 leading-tight">{{ $book->title }}</h3>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-green-100 text-green-800 uppercase tracking-wide">
                                            Completado
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 text-xs mb-2">
                                        {{ $book->authors->pluck('name')->join(', ') }}
                                    </p>
                                </div>

                                <div class="flex justify-end gap-2 mt-auto">
                                    <span class="text-green-600 text-sm font-bold flex items-center gap-1 cursor-default">
                                        <i class="fas fa-check" aria-hidden="true"></i> Leído
                                    </span>
                                </div>
                            </div>
                        </article>
                     @endforeach
                </div>
            </div>

        </div>

        <div id="reader-modal" class="fixed inset-0 z-50 hidden bg-[#0f0f12] flex-col" role="dialog" aria-modal="true" aria-labelledby="reader-title">
            <div class="h-12 bg-[#1a1a1e] border-b border-gray-800 flex justify-between items-center px-4 shadow-lg z-10">
                <h3 id="reader-title" class="text-white font-bold text-sm truncate w-1/2">Leyendo...</h3>
                <button onclick="closeReader()" class="text-gray-300 hover:text-white transition px-3 py-1 bg-red-900/30 hover:bg-red-900/50 rounded text-xs border border-red-900 cursor-pointer" aria-label="Cerrar libro">
                    <i class="fas fa-times mr-1" aria-hidden="true"></i> Cerrar
                </button>
            </div>
            <div class="flex-grow relative w-full h-full bg-[#0f0f12]">
                <div id="flipbook-container" class="absolute inset-0 w-full h-full"></div>
            </div>
        </div>
    </main>

    <script>
    var dFlipLocation = "{{ asset('dflip') }}/";

    function dragStart(event, bookId) {
        event.dataTransfer.setData("text/plain", bookId);
        event.dataTransfer.effectAllowed = "move";
        event.target.classList.add('dragging');
    }

    function allowDrop(event) {
        event.preventDefault();
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

        // Quitamos la clase de arrastre
        const allCards = document.querySelectorAll('.book-card');
        allCards.forEach(c => c.classList.remove('dragging'));

        const card = document.querySelector(`.book-card[data-id='${bookId}']`);
        const destination = newStatus === 'pending' ? document.getElementById('pending-list') : document.getElementById('completed-list');
        
        if(card && destination) {
            destination.appendChild(card);
            
            // Actualizar estilos visuales según columna
            const badge = card.querySelector('span');
            
            if(newStatus === 'completed') {
                card.classList.remove('border-blue-500');
                card.classList.add('border-green-500', 'opacity-75');
                if(badge) {
                    badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-green-100 text-green-800 uppercase tracking-wide';
                    badge.innerText = 'Completado';
                }
            } else {
                card.classList.remove('border-green-500', 'opacity-75');
                card.classList.add('border-blue-500');
                if(badge) {
                    badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-800 uppercase tracking-wide';
                    badge.innerText = 'Pendiente';
                }
            }
            updateCounters();
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            // PETICIÓN AJAX CORREGIDA (Sin /api si tu ruta web no lo tiene)
            await fetch(`/books/${bookId}/status`, { 
                method: 'POST', // Usamos POST con _method: PATCH para evitar problemas con algunos servers
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ 
                    status: newStatus,
                    _method: 'PATCH'
                })
            });
            console.log('Estado actualizado a:', newStatus);
        } catch (error) { 
            console.error('Error al actualizar:', error); 
        }
    }

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