<div>
    <div>
        <form wire:submit.prevent="importExcel">
            <input type="file" wire:model="excel" required>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded">Upload</button>
        </form>
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <!-- Display imported rows -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="row in importedRows" :key="row.id">
                <tr>
                    <td x-text="row.id"></td>
                    <td x-text="row.name"></td>
                    <td x-text="row.date"></td>
                </tr>
            </template>
        </tbody>
    </table>

    <!-- Display errors -->
    <div x-show="errors.length > 0" class="mt-4">
        <h3 class="text-red-500">Import Errors</h3>
        <ul>
            <template x-for="error in errors" :key="error">
                <li x-text="error" class="text-red-400"></li>
            </template>
        </ul>
    </div>

    <script>
        function excelImporter() {
            return {
                importedRows: [],
                errors: [],
                init() {
                    Echo.channel('excel-import')
                        .listen('RowProcessed', (e) => {
                            console.log(e)
                            if (e.error) {
                                this.errors.push(e.error);
                            } else {
                                this.importedRows.push(e.row);
                            }
                        });
                }
            }
        }
    </script>
</div>
