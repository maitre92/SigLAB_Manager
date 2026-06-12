<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuiviNotification extends Model
{
    protected $fillable = [
        'user_id',
        'emargement_id',
        'titre',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function emargement()
    {
        return $this->belongsTo(Emargement::class);
    }
}
