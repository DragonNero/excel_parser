<div x-data="excelImporter()" x-init="init">
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

    <!-- TODO: might be better to change into a progress bar -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(row, index) in importedRows" :key="index">
                <tr>
                    <td x-text="row.id"></td>
                    <td x-text="row.name"></td>
                    <td x-text="formatDate(row.date.date)"></td>
                </tr>
            </template>
        </tbody>
    </table>

    <!-- Display errors -->
    <div x-show="errors.length > 0" class="mt-4">
        <h3 class="text-red-500">Import Errors</h3>
        <ul>
            <template x-for="(error, index) in errors" :key="index">
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
                    if (this.initialized) return;

                    Echo.channel('excel-import')
                    .listen('RowProcessed', (e) => {
                        if (e.error) {
                            this.errors.push(e.error);
                        } else {
                            this.importedRows.push(e.row);
                        }
                    });

                    this.initialized = true;
                },
                formatDate(dateString) {
                    const date = new Date(dateString);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();

                    return `${day}.${month}.${year}`;
                }
            }
        }
    </script>
</div>
