<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = 'setting.' . $key;
        $value = Cache::remember($cacheKey, 3600, function () use ($key) {
            $row = static::find($key);
            return $row?->value;
        });
        return $value ?? $default;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value): void
    {
        if (is_null($value)) {
            static::where('key', $key)->delete();
        } else {
            static::updateOrCreate(
                ['key' => $key],
                ['value' => is_string($value) ? $value : json_encode($value)]
            );
        }
        Cache::forget('setting.' . $key);
    }
}
