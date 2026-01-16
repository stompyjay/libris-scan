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
        
        /* Animación para la luz del escáner */
        @keyframes scan-animation {
            0%, 100% { top: 0%; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scan {
            animation: scan-animation 2s ease-in-out infinite;
        }
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
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Mi Librería</h1>
            <p class="text-gray-400 mt-1">Gestiona tu colección digital.</p>
        </div>

        <div class="bg-[#1e2130] rounded-2xl border border-gray-800 p-8 mb-10 text-center relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500 opacity-50"></div>
            
            <h2 class="text-2xl font-bold mb-2">Escanear Nuevo Libro</h2>
            <p class="text-gray-400 mb-8 max-w-lg mx-auto">Utiliza la cámara de tu dispositivo o introduce el código manualmente para añadir un libro a tu colección.</p>

            <div class="flex flex-col md:flex-row justify-center items-center gap-8">
                
                <div class="relative w-64 h-40 bg-[#12141c] rounded-xl border-2 border-dashed border-gray-600 flex items-center justify-center group hover:border-blue-500 transition-colors cursor-pointer">
                    <div class="absolute top-0 left-0 w-full h-0.5 bg-blue-500 shadow-[0_0_15px_rgba(59,130,246,1)] animate-scan"></div>
                    
                    <div class="text-gray-500 flex flex-col items-center group-hover:text-blue-400 transition">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span class="text-sm font-mono font-bold">ACTUALIZAR CÁMARA</span>
                    </div>
                </div>

                <div class="flex flex-col gap-3 w-full md:w-auto">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-blue-900/30 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Activar Escáner
                    </button>
                    <button class="bg-[#2a2e3f] hover:bg-[#32374b] text-white font-medium py-3 px-8 rounded-xl transition border border-gray-700 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Entrada Manual
                    </button>
                </div>
            </div>
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