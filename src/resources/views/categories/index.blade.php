<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-6 px-4 sm:px-0">
                <h2 class="text-2xl font-bold text-white">Gestión de Categorías</h2>
                <a href="{{ route('categories.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-lg shadow-lg flex items-center gap-2 transition">
                    <span>+</span> Nueva Categoría
                </a>
            </div>

            <div class="bg-[#1e2130] overflow-hidden shadow-xl sm:rounded-lg border border-gray-800">
                <div class="p-6">
                    @if($categories->isEmpty())
                        <p class="text-gray-400 text-center py-10">No hay categorías registradas aún.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Descripción</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @foreach ($categories as $category)
                                <tr class="hover:bg-[#252836] transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-white font-bold">{{ $category->name }}</td>
                                    <td class="px-6 py-4 text-gray-400 text-sm">{{ $category->description ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('categories.edit', $category) }}" class="text-blue-400 hover:text-blue-300 mr-4">Editar</a>
                                        
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-400" onclick="return confirm('¿Seguro?')">
                                                Borrar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>