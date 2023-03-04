<?php

namespace App\Models;

use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class Link extends Model implements Sortable
{
    use SortableTrait, HasTranslations;

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public $translatable = ['title'];

    protected $fillable = ['title', 'link'];

    //public $cache_key = 'larabbs_links';
    protected $rememberCacheTag = 'larabbs_links';

    protected $cache_expire_in_minutes = 1440;

    public function getAllCached()
    {
        // 尝试从缓存中取出 cache_key 对应的数据。如果能取到，便直接返回数据。
        // 否则运行匿名函数中的代码来取出活跃用户数据，返回的同时做了缓存。
        // 尝试从缓存中取出 cache_key 对应的数据。如果能取到，便直接返回数据。
        // return $this->remember($this->cache_expire_in_minutes)->ordered()->get();

        // 尝试从缓存中取出 cache_key 对应的数据。如果能取到，便直接返回数据。
        $links = $this->ordered();

        if (!app()->isLocal()) {
            $this->cachePrefix .= app()->getLocale();
            $links->remember($this->cache_expire_in_minutes);
        }

        return $links->get();
    }
}