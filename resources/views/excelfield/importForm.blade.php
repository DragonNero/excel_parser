<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Import New Excel File
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto px-4">
            @livewire('excel-import')
        </div>
    </div>
</x-app-layout>
