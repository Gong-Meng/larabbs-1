<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $databaseList = DB::table('order_test')->where('type', 'order_database')->get();
        $productList  = DB::table('order_test')->where('type', 'order_product')->get();
        $filter       = [];
        foreach ($databaseList as $item) {
            $isExists = false;
            foreach ($productList as $prod) {
                if (Str::contains($item->value, $prod->value)) {
                    $isExists = true;
                }
            }
            if (!$isExists) {
                foreach ($productList as $prod) {
                    if (Str::contains($prod->value, $item->value)) {
                        $isExists = true;
                    }
                }
            }
            if (!$isExists) {
                $filter[] = [
                    'name'  => $item->name,
                    'value' => $item->value,
                ];
            }
        }

        $this->excel($filter);
    }


    public function excel(array $filter): string
    {
        $header = collect($filter)->pluck('name')->toArray();
        $data   = collect($filter)->pluck('value')->toArray();

        $path       = app()->storagePath() . '/framework/laravel-excel/';
        $config     = [
            'path' => $path,
        ];
        $fileName   = '订单列表整理' . '.xlsx';
        $excel      = new \Vtiful\Kernel\Excel($config);
        $fileObject = $excel->constMemory($fileName, NULL, false);
        $fileHandle = $fileObject->getHandle();

        $format    = new \Vtiful\Kernel\Format($fileHandle);
        $boldStyle = $format->bold()->toResource();

        $fileObject = $fileObject->setRow('A1', 10, $boldStyle) // 写入数据前设置行样式
                                 ->header($header)
                                 ->data([$data]);

        return $filepath = $fileObject->output();
    }
}
