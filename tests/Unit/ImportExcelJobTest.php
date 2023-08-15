<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Event;
use App\Jobs\ImportExcelJob;
use App\Events\RowProcessed;

class ImportExcelJobTest extends TestCase
{
    public function testSuccessfulImportOfRows()
    {
        $filePath = base_path('tests/files/valid_excel_file.xlsx');
        $fileContent = file_get_contents($filePath);

        Redis::set('excel:1', $fileContent);

        Event::fake();
        Bus::fake();

        $job = new ImportExcelJob('excel:1');
        $job->handle();

        $this->assertDatabaseHas('excel_fields', ['id' => 99999999, 'name' => 'John', 'date' => '2023-08-15']);

        Event::assertDispatched(RowProcessed::class, function ($event) {
            return $event->row !== null && $event->error === null;
        });
    }

    public function testFailedImportDueToValidationErrors()
    {
        $filePath = base_path('tests/files/invalid_excel_file.xlsx');
        $fileContent = file_get_contents($filePath);
        Redis::set('excel:2', $fileContent);

        Event::fake();
        Bus::fake();

        $job = new ImportExcelJob('excel:2');
        $job->handle();

        Event::assertDispatched(RowProcessed::class, function ($event) {
            return $event->row === null && $event->error !== null;
        });
    }
}
