<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrderImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        //
        $first = $collection->first()->toArray();

        $data = [];
        foreach ($first as $name => $value) {
            $data[] = [
                'type'  => 'order_database',
                'value' => $value,
                'name'  => $name
            ];
        }

        DB::table('order_test')->insert($data);

    }

    public function sheets(): array
    {
        return [
            'Sheet1' => new self(),
        ];
    }
}
