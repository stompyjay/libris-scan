<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Datos Personales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Nombre') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $profile->nombre ?? 'No asignado' }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Apellido') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $profile->apellido ?? 'No asignado' }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Teléfono') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $profile->telefono ?? 'No asignado' }}</p>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <a href="{{ route('profile.edit', $profile->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Editar Datos') }}
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>