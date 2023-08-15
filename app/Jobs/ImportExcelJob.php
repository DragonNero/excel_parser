<?php

namespace App\Jobs;

use App\Events\RowProcessed;
use App\Models\ExcelField;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Redis;

class ImportExcelJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    protected $redisKey;

    public function __construct($redisKey)
    {
        $this->redisKey = $redisKey;
    }

    public function handle()
    {
        $fileContent = Redis::get($this->redisKey);
        $tempFile = tmpfile();
        fwrite($tempFile, $fileContent);
        $tempPath = stream_get_meta_data($tempFile)['uri'];

        $chunks = (new FastExcel())->import($tempPath)->chunk(1000);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $row) {
                try {
                    ExcelField::create((array) $row);
                    event(new RowProcessed($row));
                } catch (\Exception $e) {
                    event(new RowProcessed(null, $e->getMessage()));
                }
            }
        }

        fclose($tempFile);
        Redis::del($this->redisKey);
    }
}
