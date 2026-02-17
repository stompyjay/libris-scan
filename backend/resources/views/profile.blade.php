<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - eBookStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Focus visible para accesibilidad */
        *:focus-visible { outline: 2px solid #60a5fa; outline-offset: 2px; }
    </style>
</head>
<body class="bg-[#121214] text-gray-200 font-sans min-h-screen">

    <main class="max-w-4xl mx-auto p-6">
        
        @if (session('status'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-2xl flex items-center gap-2" role="alert">
                <i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-between mb-10">
            <a href="{{ url('/dashboard.html') }}" class="flex items-center gap-2 text-gray-300 hover:text-white transition focus:text-white">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al Dashboard
            </a>
            <h1 class="text-xl font-bold text-blue-400">Configuración de Cuenta</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="bg-[#1a1a1e] p-8 rounded-[2.5rem] border border-gray-700 text-center h-fit shadow-xl">
                <div class="relative inline-block mb-4">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-tr from-blue-600 to-purple-600 flex items-center justify-center text-4xl shadow-2xl" aria-hidden="true">
                        👤
                    </div>
                </div>
                <h2 class="text-2xl font-black mb-1 text-white">{{ $user->name }}</h2>
                <p class="text-gray-400 text-sm mb-6">Usuario Registrado</p>
                
                <div class="bg-blue-600/10 border border-blue-600/20 rounded-2xl p-4">
                    <p class="text-blue-400 font-bold text-xs uppercase tracking-widest mb-1">Estado</p>
                    <p class="font-bold text-white">Activo</p>
                </div>
            </div>

            <div class="md:col-span-2 bg-[#1a1a1e] p-8 rounded-[2.5rem] border border-gray-700 shadow-xl">
                
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-400 uppercase ml-1 mb-2">Nombre</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                   class="w-full bg-[#121214] border border-gray-600 rounded-2xl p-4 focus:border-blue-500 outline-none transition text-white placeholder-gray-500 focus:ring-1 focus:ring-blue-500" 
                                   required>
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-400 uppercase ml-1 mb-2">Email (No editable)</label>
                            <input type="email" id="email" name="email" value="{{ $user->email }}" 
                                   class="w-full bg-[#202024] border border-gray-600 text-gray-300 rounded-2xl p-4 outline-none cursor-not-allowed font-mono text-sm opacity-80" 
                                   readonly>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="surname" class="block text-xs font-bold text-gray-400 uppercase ml-1 mb-2">Apellido</label>
                            <input type="text" id="surname" name="surname" value="{{ old('surname', $user->surname) }}" 
                                   class="w-full bg-[#121214] border border-gray-600 rounded-2xl p-4 focus:border-blue-500 outline-none transition text-white placeholder-gray-500 focus:ring-1 focus:ring-blue-500" 
                                   placeholder="Ej: Pérez">
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-bold text-gray-400 uppercase ml-1 mb-2">Teléfono</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                   class="w-full bg-[#121214] border border-gray-600 rounded-2xl p-4 focus:border-blue-500 outline-none transition text-white placeholder-gray-500 focus:ring-1 focus:ring-blue-500" 
                                   placeholder="Ej: +34 600...">
                        </div>
                    </div>

                    <div class="pt-4 flex gap-4">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-600/20 transition transform active:scale-95 focus:ring-4 focus:ring-blue-500/50">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8 bg-red-900/10 border border-red-500/20 p-8 rounded-[2.5rem]">
            <h3 class="text-red-400 font-bold mb-4 text-lg">Zona de Peligro</h3>
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="font-bold text-sm text-gray-200">Cerrar sesión</p>
                    <p class="text-gray-400 text-xs">Tendrás que volver a entrar con tu contraseña.</p>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-400 font-bold text-sm hover:text-red-300 hover:underline cursor-pointer transition focus:text-red-300 focus:underline">
                        Salir ahora
                    </button>
                </form>
            </div>
        </div>
    </main>

</body>
</html>