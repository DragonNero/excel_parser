<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Redis;
use App\Jobs\ImportExcelJob;
use Livewire\WithFileUploads;

class ExcelImport extends Component
{
    use WithFileUploads;

    public $excel;

    public function importExcel()
    {
        $this->validate([
            'excel' => 'required|file|mimes:xlsx,csv,xls'
        ]);

        $fileContent = file_get_contents($this->excel->path());
        $redisKey = 'excel_upload:' . uniqid();

        Redis::set($redisKey, $fileContent);

        ImportExcelJob::dispatch($redisKey);

        session()->flash('message', 'File is being processed!');
    }

    public function render()
    {
        return view('livewire.excel-import');
    }
}
