<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attestation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'apprenant_id',
        'formation_id',
        'date_emission',
        'statut',
        'pdf_path',
        'created_by',
    ];

    protected $casts = [
        'date_emission' => 'date',
    ];

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Générer une référence unique pour l'attestation
     */
    public static function generateReference()
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return "ATT-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
