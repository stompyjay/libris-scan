<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#1e2130] overflow-hidden shadow-xl sm:rounded-lg border border-gray-800 p-8">
                
                <h2 class="text-2xl font-bold mb-6 text-white border-b border-gray-700 pb-2">Editar Categoría</h2>
                
                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Nombre</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                            class="w-full bg-[#12141c] text-white border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Descripción</label>
                        <textarea name="description" id="description" rows="3" 
                            class="w-full bg-[#12141c] text-white border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('categories.index') }}" class="px-4 py-2 text-gray-400 hover:text-white transition">Cancelar</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition">
                            Actualizar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>