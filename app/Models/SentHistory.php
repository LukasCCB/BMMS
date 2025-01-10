<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentHistory extends Model
{
    use HasFactory;

    protected $table = 'sent_histories';

    protected $fillable = [
        'number',
        'content',
        'sent_by',
        'has_sent',
        'failed_message',
    ];

    protected $casts = [
        'has_sent' => 'boolean',
        'content' => 'array',
    ];
}
