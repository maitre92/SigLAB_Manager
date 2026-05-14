<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'formation_id',
        'titre',
        'type',
        'date_evaluation',
        'coefficient',
        'description',
        'statut',
    ];

    protected $casts = [
        'date_evaluation' => 'datetime',
        'coefficient' => 'decimal:2',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
