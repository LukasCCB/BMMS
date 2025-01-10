<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    protected $table = 'apps';

    protected $fillable = [
        'sender_block',
        'appkey',
        'authkey',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public static function userApps($name)
    {
        return self::where('sender_block', $name)->where('is_active', true)->get();
    }
}
