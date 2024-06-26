<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class APIKEY extends Model
{
    protected $table = 'api_keys';
    protected string $public_key_prefix = 'pk_';
    protected string $secret_key_prefix = 'sk_';
    protected string $test_public_key_prefix = 'pk_test_';
    protected string $test_secret_key_prefix = 'sk_test_';

    protected $fillable = [
        'user_id', 'live_public_key', 'test_public_key', 'live_secret_key', 'test_secret_key', 'status', 'activate_at',
        'deactivate_at'
    ];

    protected $casts = [
        'activate_at' => 'datetime',
        'deactivate_at' => 'datetime',
    ];

    public static function livePublicKey(): string
    {
        return self::generateKey('public_key_prefix', 'live_public_key');
    }

    public static function testPublicKey(): string
    {
        return self::generateKey('test_public_key_prefix', 'test_public_key');
    }

    public static function liveSecretKey(): string
    {
        return self::generateKey('secret_key_prefix', 'live_secret_key');
    }

    public static function testSecretKey(): string
    {
        return self::generateKey('test_secret_key_prefix', 'test_secret_key');
    }

    private static function generateKey(string $prefixProperty, string $keyColumn): string
    {
        $self = new static; // Object instantiation to access the prefix properties
        $prefix = $self->$prefixProperty;
        $key = Str::random(32);
        $fullKey = $prefix.$key;

        // Check for existing key and regenerate if it exists
        if (self::where($keyColumn, $fullKey)->exists()) {
            return self::generateKey($prefixProperty, $keyColumn);
        }

        return $fullKey;
    }
}
