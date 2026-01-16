<x-app-layout>
    <div class="py-12 bg-[#12141c] min-h-screen text-white">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white mb-6 inline-block flex items-center gap-2">
                <span>&larr;</span> Volver al Dashboard
            </a>

            <div class="bg-[#1e2130] rounded-2xl border border-gray-800 p-8 shadow-xl mb-10">
                
                <h2 class="text-3xl font-bold text-white mb-2 text-center">Biblioteca Global</h2>
                <p class="text-gray-400 mb-8 text-center">Busca un libro específico o descubre algo nuevo.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    <div class="border-b md:border-b-0 md:border-r border-gray-700 pb-6 md:pb-0 md:pr-6">
                        <label class="block text-lg font-bold text-blue-400 mb-3">🔍 Buscar por Título</label>
                        <p class="text-sm text-gray-500 mb-4">Ideal si ya sabes qué libro quieres guardar.</p>
                        
                        <form action="{{ route('books.process') }}" method="POST" class="relative">
                            @csrf
                            <input type="text" name="query" 
                                class="w-full bg-[#12141c] text-white border border-gray-700 rounded-lg py-4 px-4 focus:ring-2 focus:ring-blue-500 outline-none"
                                placeholder="Ej: Harry Potter, El Hobbit...">
                            <button type="submit" class="absolute right-3 top-3 bg-blue-600 p-2 rounded-md hover:bg-blue-500 text-white transition">
                                Buscar
                            </button>
                        </form>
                    </div>

                    <div class="pt-6 md:pt-0 md:pl-6">
                        <label class="block text-lg font-bold text-green-400 mb-3">🎲 Explorar por Género</label>
                        <p class="text-sm text-gray-500 mb-4">Elige un tema y te mostramos los más populares.</p>

                        <form action="{{ route('books.browse') }}" method="GET">
                            <select name="category" onchange="this.form.submit()" 
                                class="w-full bg-[#12141c] text-white border border-gray-700 rounded-lg py-4 px-4 focus:ring-2 focus:ring-green-500 outline-none cursor-pointer hover:border-green-500 transition">
                                <option value="" disabled selected>Selecciona una categoría...</option>
                                <option value="fantasy">🧙‍♂️ Fantasía</option>
                                <option value="science_fiction">🚀 Ciencia Ficción</option>
                                <option value="mystery_and_detective_stories">🕵️‍♂️ Misterio</option>
                                <option value="romance">💘 Romance</option>
                                <option value="horror">👻 Terror</option>
                                <option value="history">🏛️ Historia</option>
                                <option value="biography">👤 Biografías</option>
                                <option value="finance">💰 Finanzas</option>
                            </select>
                        </form>
                    </div>

                </div>
            </div>

            @if(isset($books) && count($books) > 0)
                <h3 class="text-2xl font-bold mb-6 flex items-center gap-3">
                    <span class="bg-green-600 text-white text-sm px-3 py-1 rounded-full">Resultados</span>
                    <span class="text-gray-300">Categoría:</span> 
                    <span class="text-green-400 uppercase">{{ $selectedCategory ?? 'General' }}</span>
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    @foreach($books as $book)
                        <div class="bg-[#1e2130] rounded-xl overflow-hidden border border-gray-800 hover:border-green-500 transition group relative shadow-lg">
                            <div class="h-56 bg-gray-900 overflow-hidden flex items-center justify-center relative">
                                @if($book['cover_id'])
                                    <img src="https://covers.openlibrary.org/b/id/{{ $book['cover_id'] }}-L.jpg" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                                @else
                                    <span class="text-4xl opacity-30">📖</span>
                                @endif
                                
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <form action="{{ route('books.process') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="query" value="{{ $book['title'] }}">
                                        <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded-full shadow-lg transform scale-90 group-hover:scale-100 transition">
                                            Guardar +
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h4 class="font-bold text-white text-sm line-clamp-2 min-h-[2.5rem] leading-tight">{{ $book['title'] }}</h4>
                                <p class="text-gray-400 text-xs mt-2">{{ $book['author'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>