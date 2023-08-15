<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight w-5/6">
            {{ __('excelField') }}
        </h2>
        <a href="{{ route('excelFieldImport') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded w-1/6">Import New Excel</a>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto px-4">
            @foreach($excelFields->groupBy('date') as $date => $fields)
                <div class="mb-6">
                    <h2 class="text-2xl font-bold mb-3">{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</h2>

                    <table class="min-w-full bg-white shadow-md rounded">
                        <thead>
                            <tr>
                                <th class="w-1/6 py-2 px-3 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                <th class="w-4/6 py-2 px-3 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                <th class="w-1/6 py-2 px-3 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fields as $field)
                                <tr>
                                    <td class="py-2 px-3 border-b border-gray-200">{{ $field->id }}</td>
                                    <td class="py-2 px-3 border-b border-gray-200">{{ $field->name }}</td>
                                    <td class="py-2 px-3 border-b border-gray-200">{{ \Carbon\Carbon::parse($field->date)->format('d.m.Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            <div class="mt-4">
                {{ $excelFields->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
