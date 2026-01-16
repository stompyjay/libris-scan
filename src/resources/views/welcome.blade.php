<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Libris-Scan</title>
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        
        <script src="https://cdn.tailwindcss.com"></script>
        
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Figtree', 'sans-serif'],
                        },
                    }
                }
            }
        </script>
    </head>
    <body class="antialiased bg-gray-900 text-white font-sans selection:bg-blue-500 selection:text-white">
        
        <nav class="flex items-center justify-between p-6 max-w-7xl mx-auto">
            <div class="text-xl font-bold tracking-wide text-white">
                Libris-Scan
            </div>
            
            @if (Route::has('login'))
                <div class="z-10 flex gap-4"> @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-white bg-blue-600 px-4 py-2 rounded hover:bg-blue-700 transition">
                            Ir al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-300 hover:text-white transition py-2">
                            Iniciar Sesión
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="font-semibold text-white border border-gray-500 px-4 py-2 rounded hover:bg-gray-800 transition">
                                Registrarse
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>

        <main class="max-w-7xl mx-auto px-6 flex flex-col items-center text-center mt-10">
            
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight mb-8 leading-tight max-w-2xl text-white">
                Digitaliza tu librería <br />
                <span class="text-gray-400">con un simple escaneo</span>
            </h1>

            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto mb-12">
                
                <a href="{{ route('register') }}" class="flex items-center justify-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-bold transition shadow-lg w-full sm:w-64 cursor-pointer">
                    Comenzar Ahora
                </a>
                
                <button class="flex items-center justify-center px-8 py-3 bg-blue-500 bg-opacity-20 hover:bg-opacity-30 border border-blue-500 text-blue-400 rounded-full font-bold transition w-full sm:w-64">
                    Ver Demo
                </button>
            </div>

            <div class="relative w-64 h-96 sm:w-72 sm:h-[28rem] bg-gray-800 rounded-3xl border-4 border-gray-700 shadow-2xl overflow-hidden mb-16 flex flex-col mx-auto">
                <div class="absolute top-0 left-0 w-full h-full bg-gray-700 opacity-60"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-48 h-32 border-2 border-blue-400 rounded-lg relative animate-pulse">
                        <div class="absolute top-0 left-0 w-4 h-4 border-t-4 border-l-4 border-white -mt-1 -ml-1"></div>
                        <div class="absolute top-0 right-0 w-4 h-4 border-t-4 border-r-4 border-white -mt-1 -mr-1"></div>
                        <div class="absolute bottom-0 left-0 w-4 h-4 border-b-4 border-l-4 border-white -mb-1 -ml-1"></div>
                        <div class="absolute bottom-0 right-0 w-4 h-4 border-b-4 border-r-4 border-white -mb-1 -mr-1"></div>
                        <div class="absolute bottom-2 left-0 right-0 text-center text-xs font-mono text-white bg-black bg-opacity-50 mx-2 rounded">Scanning ISBN...</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full max-w-4xl mb-20 text-left mx-auto">
                <div class="bg-gray-800 bg-opacity-50 p-6 rounded-2xl border border-gray-700 flex items-start gap-4">
                    <div class="bg-gray-700 p-3 rounded-xl shrink-0 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white mb-1">Organización Automática</h3>
                        <p class="text-sm text-gray-400">Clasifica tus libros automáticamente.</p>
                    </div>
                </div>

                <div class="bg-gray-800 bg-opacity-50 p-6 rounded-2xl border border-gray-700 flex items-start gap-4">
                    <div class="bg-gray-700 p-3 rounded-xl shrink-0 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white mb-1">Control Total</h3>
                        <p class="text-sm text-gray-400">Gestiona autores y reseñas.</p>
                    </div>
                </div>
            </div>

        </main>
    </body>
</html>