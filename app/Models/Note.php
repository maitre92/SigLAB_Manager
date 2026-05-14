<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'apprenant_id',
        'valeur',
        'commentaire',
    ];

    protected $casts = [
        'valeur' => 'decimal:2',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }
}
