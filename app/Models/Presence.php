<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'formation_id',
        'apprenant_id',
        'date',
        'statut',
        'commentaire',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }
}
