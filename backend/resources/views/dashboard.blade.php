<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Libris-Scan</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#12141c] text-white min-h-screen">

    <nav class="border-b border-gray-800 bg-[#1a1d29]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                        </svg>
                    </div>
                    <span class="font-bold text-lg tracking-tight">Libris-Scan</span>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-gray-400 text-sm hidden sm:block">Hola, {{ Auth::user()->name }}</span>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-gray-800 hover:bg-red-900/50 text-gray-300 hover:text-red-400 px-3 py-1.5 rounded-lg text-sm font-medium transition border border-gray-700">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold">Mi Librería</h1>
                <p class="text-gray-400 mt-1">Gestiona tu colección digital.</p>
            </div>
            
            {{-- AQUI ESTA EL CAMBIO: Ahora usamos 'a' y route('books.search') --}}
            <a href="{{ route('books.search') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full font-bold shadow-lg shadow-blue-600/20 flex items-center gap-2 transition transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Escanear Nuevo Libro
            </a>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <div class="bg-[#1e2130] p-6 rounded-2xl border border-gray-800">
                <div class="text-gray-400 text-sm font-medium mb-1">Total Libros</div>
                <div class="text-3xl font-bold">12</div>
            </div>
            <div class="bg-[#1e2130] p-6 rounded-2xl border border-gray-800">
                <div class="text-gray-400 text-sm font-medium mb-1">Leídos este año</div>
                <div class="text-3xl font-bold text-green-400">5</div>
            </div>
            <div class="bg-[#1e2130] p-6 rounded-2xl border border-gray-800">
                <div class="text-gray-400 text-sm font-medium mb-1">Prestados</div>
                <div class="text-3xl font-bold text-yellow-400">2</div>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-xl font-bold mb-4 text-white flex items-center gap-2">
                <span>🗂️</span> Categorías Disponibles
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                
                @if(!isset($categories) || $categories->isEmpty())
                    <div class="col-span-3 border border-dashed border-gray-700 rounded-xl p-8 text-center bg-[#1a1d29]">
                        <p class="text-gray-400 text-lg">No se encontraron categorías.</p>
                        <p class="text-gray-500 text-sm mt-2">
                            Asegúrate de haber creado alguna en la base de datos y de que la ruta en 
                            <code class="bg-gray-800 px-1 py-0.5 rounded text-blue-300">web.php</code> 
                            esté enviando la variable <code class="text-blue-300">$categories</code>.
                        </p>
                    </div>
                @else
                    @foreach($categories as $category)
                        <div class="bg-[#1e2130] p-5 rounded-xl border border-gray-800 hover:border-blue-500 transition cursor-pointer group shadow-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-lg text-white group-hover:text-blue-400 transition">
                                        {{ $category->name }}
                                    </h3>
                                    <p class="text-sm text-gray-400 mt-1 line-clamp-2">
                                        {{ $category->description ?? 'Sin descripción' }}
                                    </p>
                                </div>
                                <div class="bg-gray-800 p-2 rounded-lg text-gray-400 group-hover:bg-blue-600 group-hover:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
        </div>

    </main>
</body>
</html>