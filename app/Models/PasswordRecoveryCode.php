<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordRecoveryCode extends Model
{
    protected $fillable = [
        'user_id',
        'code_hash',
        'attempts',
        'expires_at',
        'last_sent_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'expires_at' => 'datetime',
            'last_sent_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }
}
