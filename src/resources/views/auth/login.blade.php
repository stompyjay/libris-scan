<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Libris-Scan</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#12141c] text-white flex flex-col items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-sm flex flex-col items-center">

        <div class="flex flex-col items-center mb-8">
            <div class="bg-blue-500 p-4 rounded-2xl mb-4 shadow-lg shadow-blue-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.25 4.533A9.707 9.707 0 006 3a9.735 9.735 0 00-3.25.555.75.75 0 00-.5.707v14.25a.75.75 0 001 .707A8.237 8.237 0 016 18.75c1.995 0 3.823.707 5.25 1.886V4.533zM12.75 20.636A8.214 8.214 0 0118 18.75c.966 0 1.89.166 2.75.47a.75.75 0 001-.708V4.262a.75.75 0 00-.5-.707A9.735 9.735 0 0018 3a9.707 9.707 0 00-5.25 1.533v16.103z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Libris-Scan</h1>
        </div>

        <div class="w-full border border-gray-700 rounded-2xl p-6 bg-transparent">
            
            @if ($errors->any())
                <div class="mb-4 text-sm text-red-400 text-center bg-red-900/30 p-2 rounded">
                    Credenciales incorrectas
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                @csrf

                <div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="Email"
                        class="w-full bg-white text-gray-900 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500" />
                </div>

                <div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        placeholder="Password"
                        class="w-full bg-white text-gray-900 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500" />
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg px-4 py-3 transition mt-2 shadow-lg shadow-blue-600/30">
                    Log in
                </button>

                <div class="text-center mt-2">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-gray-500 hover:text-gray-300 transition">
                            Forgot password?
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="w-full flex gap-4 mt-8">
            <button class="flex-1 bg-white text-black font-semibold rounded-xl py-3 flex items-center justify-center gap-2 hover:bg-gray-200 transition text-sm">
                <span class="text-xl"></span> Apple
            </button>

            <button class="flex-1 bg-white text-black font-semibold rounded-xl py-3 flex items-center justify-center gap-2 hover:bg-gray-200 transition text-sm">
                <span class="text-lg">G</span> Google
            </button>
        </div>
    </div>
</body>
</html>