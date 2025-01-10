<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Number extends Model
{
    use HasFactory;

    protected $table = 'numbers';

    protected $fillable = [
        'name',
        'slug',
        'number',
        'is_active',
        'is_whatsapp',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_whatsapp' => 'boolean'
    ];

    public static function boot (): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    public function getNumberAttribute($value): string
    {
        // Remove o '55' se o número começar com ele
        if (Str::startsWith($value, '55')) {
            $value = substr($value, 2);  // Remove o '55'
        }

        // Verifica se o número tem mais de 10 dígitos
        if (strlen($value) > 10) {
            // Remove o terceiro dígito (neste caso, remove o nono dígito do código de área)
            $value = substr($value, 0, 2) . substr($value, 3);
        }

        // Retorna o número com '55' no início
        return '55' . $value;
    }
}
