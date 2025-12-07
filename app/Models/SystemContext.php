<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemContext extends Model
{
    protected $fillable = ['key', 'content'];

    public static function contentByKey(string $key): ?string
    {
        $row = static::query()->where('key', $key)->first();
        return $row ? (string) $row->content : null;
    }

    public static function cachedContent(string $key): ?string
    {
        $cacheKey = 'system_context:' . $key;
        return Cache::rememberForever($cacheKey, function () use ($key) {
            $row = static::query()->where('key', $key)->first();
            return $row ? (string) $row->content : null;
        });
    }

    protected static function booted(): void
    {
        static::saved(function (self $model) {
            Cache::forget('system_context:' . $model->key);
        });
        static::deleted(function (self $model) {
            Cache::forget('system_context:' . $model->key);
        });
    }
}
