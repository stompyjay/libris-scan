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
        ::-webkit-scrollbar-thumb { background: #6b7280; border-radius: 4px; }
        
        .drop-zone { transition: all 0.3s ease; }
        .drag-over {
            background-color: rgba(37, 99, 235, 0.2) !important;
            border-color: #60a5fa !important;
            transform: scale(1.01);
        }
        
        .dragging { opacity: 1; border: 2px dashed #3b82f6; background-color: #eff6ff; } 
        
        *:focus-visible { outline: 3px solid #93c5fd; outline-offset: 2px; }
    </style>
</head>

<body class="bg-[#0f0f12] text-gray-100 font-sans min-h-screen flex flex-col">

    <nav class="bg-[#1a1a1e] border-b border-gray-600 p-4 sticky top-0 z-40 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-columns text-blue-300" aria-hidden="true"></i> Mi Tablero de Lectura
            </h1>
            <a href="{{ url('/dashboard.html') }}" class="text-sm font-bold text-white hover:text-blue-200 transition flex items-center gap-2 underline decoration-white underline-offset-4">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al Dashboard
            </a>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8">
        
        <div class="flex justify-end mb-6">
            <div class="bg-gray-800 p-1 rounded-lg border border-gray-600 flex gap-1">
                <button onclick="switchView('kanban')" id="btn-kanban" class="px-4 py-2 rounded-md bg-blue-700 text-white shadow transition-all flex items-center gap-2 text-sm font-bold border-2 border-transparent focus:border-white" aria-label="Vista de Tablero">
                    <i class="fas fa-columns"></i> <span class="hidden sm:inline">Tablero</span>
                </button>
                <button onclick="switchView('table')" id="btn-table" class="px-4 py-2 rounded-md text-white hover:bg-gray-700 transition-all flex items-center gap-2 text-sm font-bold border-2 border-transparent focus:border-white" aria-label="Vista de Tabla">
                    <i class="fas fa-list"></i> <span class="hidden sm:inline">Lista</span>
                </button>
            </div>
        </div>

        <div id="view-kanban" class="animate-fade-in">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 h-full">

                <div class="flex flex-col h-full">
                    <div class="bg-blue-900 border-t-4 border-blue-400 p-3 rounded-t-lg flex justify-between items-center shadow-sm">
                        <h2 class="font-bold text-white uppercase tracking-wider text-sm flex items-center">
                            <i class="fas fa-hourglass-half mr-2 text-white" aria-hidden="true"></i> Por Leer / Leyendo
                        </h2>
                        <span id="count-pending" class="bg-white text-black text-xs px-2 py-1 rounded-full font-black border border-gray-300">
                            {{ $books->where('status', '!=', 'completed')->count() }}
                        </span>
                    </div>
                    
                    <div id="pending-list" 
                         class="drop-zone flex-grow bg-[#121215] border-x border-b border-gray-600 rounded-b-lg p-4 space-y-3 min-h-[500px]"
                         ondragover="allowDrop(event)"
                         ondragleave="removeDragStyle(event)"
                         ondrop="handleDrop(event, 'pending')">
                         
                         @forelse($books->where('status', '!=', 'completed') as $book)
                            <article class="book-card bg-white p-3 rounded-lg shadow-md mb-4 border-l-4 border-blue-700 flex gap-4"
                                     draggable="true"
                                     data-id="{{ $book->id }}"
                                     data-title="{{ $book->title }}"
                                     data-pdf="path/to/pdf.pdf"
                                     ondragstart="dragStart(event, '{{ $book->id }}')"
                                     tabindex="0"
                                     aria-label="Libro: {{ $book->title }}">
                                
                                <div class="flex-shrink-0">
                                    <img src="{{ $book->cover ?? 'https://via.placeholder.com/100x150?text=No+Cover' }}" 
                                         alt="Portada de {{ $book->title }}" 
                                         loading="lazy" width="80" height="112"
                                         class="w-20 h-28 object-cover rounded shadow-sm border border-gray-300">
                                </div>

                                <div class="flex-grow flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-black text-base text-black leading-tight">{{ $book->title }}</h3>
                                            
                                            <span class="status-badge text-[11px] font-bold px-2 py-1 rounded bg-blue-800 text-white uppercase tracking-wide">
                                                {{ $book->pivot->status ?? 'Pendiente' }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-gray-900 text-xs mb-2 font-bold">
                                            @if($book->authors && $book->authors->count() > 0)
                                                {{ $book->authors->pluck('name')->join(', ') }}
                                            @else
                                                Autor desconocido
                                            @endif
                                        </p>
                                    </div>

                                    <div class="card-footer flex justify-end gap-2 mt-auto">
                                        <button onclick="openReader('{{ $book->title }}', 'path/to/pdf.pdf')" 
                                                class="bg-blue-800 text-white hover:bg-blue-900 text-sm font-bold flex items-center gap-2 transition px-4 py-2 rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                                aria-label="Leer {{ $book->title }}">
                                                <i class="fas fa-book-open" aria-hidden="true"></i> LEER
                                        </button>
                                    </div>
                                </div>
                            </article>
                         @empty
                            <p class="text-center text-gray-300 mt-10 font-bold">Arrastra libros aquí para leerlos.</p>
                         @endforelse
                    </div>
                </div>

                <div class="flex flex-col h-full">
                    <div class="bg-green-900 border-t-4 border-green-400 p-3 rounded-t-lg flex justify-between items-center shadow-sm">
                        <h2 class="font-bold text-white uppercase tracking-wider text-sm flex items-center">
                            <i class="fas fa-check-circle mr-2 text-white" aria-hidden="true"></i> Terminados
                        </h2>
                        <span id="count-completed" class="bg-white text-black text-xs px-2 py-1 rounded-full font-black border border-gray-300">
                            {{ $books->where('status', 'completed')->count() }}
                        </span>
                    </div>

                    <div id="completed-list" 
                         class="drop-zone flex-grow bg-[#121215] border-x border-b border-gray-600 rounded-b-lg p-4 space-y-3 min-h-[500px]"
                         ondragover="allowDrop(event)"
                         ondragleave="removeDragStyle(event)"
                         ondrop="handleDrop(event, 'completed')">
                         
                         @foreach($books->where('status', 'completed') as $book)
                            <article class="book-card bg-gray-50 p-3 rounded-lg shadow-md mb-4 border-l-4 border-green-700 flex gap-4"
                                     draggable="true"
                                     data-id="{{ $book->id }}"
                                     data-title="{{ $book->title }}"
                                     data-pdf="path/to/pdf.pdf"
                                     ondragstart="dragStart(event, '{{ $book->id }}')"
                                     tabindex="0"
                                     aria-label="Libro completado: {{ $book->title }}">
                                
                                <div class="flex-shrink-0">
                                    <img src="{{ $book->cover ?? 'https://via.placeholder.com/100x150?text=No+Cover' }}" 
                                         alt="Portada de {{ $book->title }}" 
                                         loading="lazy" width="80" height="112"
                                         class="w-20 h-28 object-cover rounded shadow-sm grayscale hover:grayscale-0 transition border border-gray-300">
                                </div>

                                <div class="flex-grow flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-black text-base text-black leading-tight">{{ $book->title }}</h3>
                                            
                                            <span class="status-badge text-[11px] font-bold px-2 py-1 rounded bg-green-800 text-white uppercase tracking-wide">
                                                Completado
                                            </span>
                                        </div>
                                        
                                        <p class="text-gray-900 text-xs mb-2 font-bold">
                                            {{ $book->authors->pluck('name')->join(', ') }}
                                        </p>
                                    </div>

                                    <div class="card-footer flex justify-end gap-2 mt-auto">
                                        <span class="text-green-900 text-sm font-black flex items-center gap-1 cursor-default bg-green-100 px-3 py-1 rounded border border-green-300">
                                            <i class="fas fa-check" aria-hidden="true"></i> Leído
                                        </span>
                                    </div>
                                </div>
                            </article>
                         @endforeach
                    </div>
                </div>

            </div>
        </div>

        <div id="view-table" class="hidden animate-fade-in">
            <div class="bg-[#1a1a1e] rounded-xl border border-gray-600 shadow-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-black text-white text-xs uppercase tracking-wider border-b border-gray-600">
                                <th class="p-4 font-bold">Portada</th>
                                <th class="p-4 font-bold">Título</th>
                                <th class="p-4 font-bold">Autor</th>
                                <th class="p-4 font-bold">Estado</th>
                                <th class="p-4 font-bold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 text-gray-200 text-sm">
                            @foreach($books as $book)
                            <tr class="hover:bg-gray-800 transition duration-150 group">
                                <td class="p-4 w-20">
                                    <img src="{{ $book->cover ?? 'https://via.placeholder.com/60x90' }}" 
                                         class="w-12 h-16 object-cover rounded shadow-md border border-gray-600" 
                                         alt="Portada">
                                </td>
                                <td class="p-4 font-bold text-white text-base">
                                    {{ $book->title }}
                                </td>
                                <td class="p-4 font-medium text-gray-300">
                                    {{ $book->authors->pluck('name')->join(', ') ?: 'Desconocido' }}
                                </td>
                                <td class="p-4">
                                    @if($book->pivot->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-900 text-white border border-green-500">
                                            <span class="w-2 h-2 rounded-full bg-green-400"></span> Completado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-900 text-white border border-blue-500">
                                            <span class="w-2 h-2 rounded-full bg-blue-400"></span> Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <button onclick="openReader('{{ $book->title }}', 'path/to/pdf')" 
                                            class="bg-blue-700 hover:bg-blue-600 text-white border border-blue-500 px-3 py-1.5 rounded-lg transition text-xs font-bold inline-flex items-center gap-2 shadow-sm focus:ring-2 focus:ring-white">
                                        <i class="fas fa-book-open"></i> Leer
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="reader-modal" class="fixed inset-0 z-50 hidden bg-[#0f0f12] flex-col" role="dialog" aria-modal="true" aria-labelledby="reader-title">
            <div class="h-14 bg-[#1a1a1e] border-b border-gray-600 flex justify-between items-center px-4 shadow-lg z-10">
                <h3 id="reader-title" class="text-white font-bold text-base truncate w-1/2">Leyendo...</h3>
                <button onclick="closeReader()" class="text-white hover:text-white transition px-4 py-2 bg-red-700 hover:bg-red-600 rounded text-sm font-bold border border-red-500 shadow-md cursor-pointer focus:ring-2 focus:ring-red-400" aria-label="Cerrar libro">
                    <i class="fas fa-times mr-2" aria-hidden="true"></i> Cerrar
                </button>
            </div>
            <div class="flex-grow relative w-full h-full bg-[#0f0f12]">
                <div id="flipbook-container" class="absolute inset-0 w-full h-full"></div>
            </div>
        </div>
    </main>

    <script>
    var dFlipLocation = "{{ asset('dflip') }}/";

    function switchView(viewName) {
        const kanban = document.getElementById('view-kanban');
        const table = document.getElementById('view-table');
        const btnKanban = document.getElementById('btn-kanban');
        const btnTable = document.getElementById('btn-table');

        if (viewName === 'kanban') {
            kanban.classList.remove('hidden');
            table.classList.add('hidden');
            
            btnKanban.className = "px-4 py-2 rounded-md bg-blue-700 text-white shadow transition-all flex items-center gap-2 text-sm font-bold border-2 border-transparent focus:border-white";
            btnTable.className = "px-4 py-2 rounded-md text-white hover:bg-gray-700 transition-all flex items-center gap-2 text-sm font-bold border-2 border-transparent focus:border-white";
        } else {
            kanban.classList.add('hidden');
            table.classList.remove('hidden');
            
            btnTable.className = "px-4 py-2 rounded-md bg-blue-700 text-white shadow transition-all flex items-center gap-2 text-sm font-bold border-2 border-transparent focus:border-white";
            btnKanban.className = "px-4 py-2 rounded-md text-white hover:bg-gray-700 transition-all flex items-center gap-2 text-sm font-bold border-2 border-transparent focus:border-white";
        }
    }

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

        const allCards = document.querySelectorAll('.book-card');
        allCards.forEach(c => c.classList.remove('dragging'));

        const card = document.querySelector(`.book-card[data-id='${bookId}']`);
        const destination = newStatus === 'pending' ? document.getElementById('pending-list') : document.getElementById('completed-list');
        
        if(card && destination) {
            destination.appendChild(card);
            
            // Elementos y datos para actualizar
            const badge = card.querySelector('.status-badge');
            const footer = card.querySelector('.card-footer');
            const title = card.getAttribute('data-title');
            const pdfUrl = card.getAttribute('data-pdf');
            
            if(newStatus === 'completed') {
                // CAMBIAR A ESTILO COMPLETADO
                card.classList.remove('border-blue-700', 'bg-white');
                card.classList.add('border-green-700', 'bg-gray-50'); 
                
                // Actualizar Badge
                if(badge) {
                    badge.className = 'status-badge text-[11px] font-bold px-2 py-1 rounded bg-green-800 text-white uppercase tracking-wide';
                    badge.innerText = 'Completado';
                }

                // Actualizar Footer (Quitar botón leer, poner etiqueta Leído)
                if(footer) {
                    footer.innerHTML = `
                        <span class="text-green-900 text-sm font-black flex items-center gap-1 cursor-default bg-green-100 px-3 py-1 rounded border border-green-300">
                            <i class="fas fa-check" aria-hidden="true"></i> Leído
                        </span>`;
                }
            } else {
                // CAMBIAR A ESTILO PENDIENTE
                card.classList.remove('border-green-700', 'bg-gray-50');
                card.classList.add('border-blue-700', 'bg-white');
                
                // Actualizar Badge
                if(badge) {
                    badge.className = 'status-badge text-[11px] font-bold px-2 py-1 rounded bg-blue-800 text-white uppercase tracking-wide';
                    badge.innerText = 'Pendiente';
                }

                // Actualizar Footer (Poner botón leer de nuevo)
                if(footer) {
                    footer.innerHTML = `
                        <button onclick="openReader('${title}', '${pdfUrl}')" 
                                class="bg-blue-800 text-white hover:bg-blue-900 text-sm font-bold flex items-center gap-2 transition px-4 py-2 rounded shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-book-open" aria-hidden="true"></i> LEER
                        </button>`;
                }
            }
            updateCounters();
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            await fetch(`/books/${bookId}/status`, { 
                method: 'POST', 
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