<?php

namespace App\Jobs;

use App\Events\RowProcessed;
use App\Models\ExcelField;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ImportExcelJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    protected $redisKey;
    protected $processedRowsKey;

    public function __construct($redisKey)
    {
        $this->redisKey = $redisKey;
        $this->processedRowsKey = 'processed_rows:' . $redisKey;
    }

    public function handle()
    {
        $fileContent = Redis::get($this->redisKey);
        $tempFile = tmpfile();
        fwrite($tempFile, $fileContent);
        $tempPath = stream_get_meta_data($tempFile)['uri'];

        Redis::set($this->processedRowsKey, 0);

        $chunks = (new FastExcel())->import($tempPath)->chunk(1000);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $row) {
                $rowData = (array) $row;

                $validator = Validator::make((array) $rowData, [
                    'id' => 'required|unique:excel_fields',
                    'name' => 'required|max:255',
                    'date' => 'required',
                ]);

                if ($validator->fails()) {
                    event(new RowProcessed(null, $validator->errors()->toJson()));
                } else {
                    try {
                        ExcelField::create((array) $rowData);
                        event(new RowProcessed($rowData));
                    } catch (\Exception $e) {
                        event(new RowProcessed(null, $e->getMessage()));
                    }
                }
            }

            $processed = Redis::get($this->processedRowsKey);
            Redis::set($this->processedRowsKey, $processed + 1000);
        }

        fclose($tempFile);
        Redis::del($this->redisKey);
        Redis::del($this->processedRowsKey);
    }
}
