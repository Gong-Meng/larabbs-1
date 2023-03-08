<?php

namespace App\Imports;

use App\Models\Topic;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Importable;

class TopicsImport implements ToModel, WithHeadingRow, WithMultipleSheets
{
    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $topic = new Topic([
            'title' => $row['标题'],
            'category_id' => (int)$row['分类id'],
        ]);
        $topic->user_id = (int)$row['用户id'];

        return $topic;
    }

    public function sheets(): array
    {
        return [
            'topics' => new self(),
        ];
    }
}
