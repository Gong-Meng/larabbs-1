<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;

class TopicFilter extends ModelFilter
{
    public $relations = [
        'replies' => ['replyer_id']
    ];

    public function setup()
    {
        // 如果没有传 order，则默认使用 default
        if (!$this->input('order'))  {
            $this->push('order', 'default');
        }
    }

    public function title($value)
    {
        return $this->whereLike('title', $value);
    }

    public function minViewCount($minCount)
    {
        return $this->where('view_count', '>=', $minCount);
    }

    // public function replyer($id)
    // {
    //     return $this->whereHas('replies', function($query) use($id) {
    //         return $query->where('user_id', $id);
    //     });
    // }

    public function category($id)
    {
        return $this->where('category_id', $id);
    }

    public function order($value)
    {
        switch ($value) {
            case 'recent':
                $this->recent();
                break;
            default:
                $this->recentReplied();
                break;
        }
    }
}