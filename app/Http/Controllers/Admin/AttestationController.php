<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use App\Models\Attestation;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttestationController extends Controller
{
    /**
     * Liste des attestations générées et des étudiants éligibles.
     */
    public function index()
    {
        $page_title = 'Gestion des Attestations';
        $attestations = Attestation::with(['apprenant', 'formation'])->latest()->get();
        
        // On récupère les formations terminées pour proposer la génération
        $formations = Formation::where('statut', 'terminee')->get();

        return view('admin.attestations.index', compact('attestations', 'formations', 'page_title'));
    }

    /**
     * Formulaire pour générer une attestation pour un apprenant spécifique.
     */
    public function create(Request $request)
    {
        $page_title = 'Générer une Attestation';
        $apprenantId = $request->apprenant_id;
        $formationId = $request->formation_id;

        $apprenant = Apprenant::findOrFail($apprenantId);
        $formation = Formation::findOrFail($formationId);

        return view('admin.attestations.create', compact('apprenant', 'formation', 'page_title'));
    }

    /**
     * Enregistrer l'attestation en base.
     */
    public function store(Request $request)
    {
        $request->validate([
            'apprenant_id' => 'required|exists:apprenants,id',
            'formation_id' => 'required|exists:formations,id',
            'date_emission' => 'required|date',
        ]);

        // Vérifier si une attestation existe déjà pour ce couple
        $exists = Attestation::where('apprenant_id', $request->apprenant_id)
            ->where('formation_id', $request->formation_id)
            ->first();

        if ($exists) {
            return redirect()->route('admin.attestations.index')->with('error', 'Une attestation existe déjà pour cet apprenant sur cette formation.');
        }

        Attestation::create([
            'reference' => Attestation::generateReference(),
            'apprenant_id' => $request->apprenant_id,
            'formation_id' => $request->formation_id,
            'date_emission' => $request->date_emission,
            'statut' => 'genere',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.attestations.index')->with('success', 'Attestation générée avec succès.');
    }

    /**
     * Afficher/Visualiser l'attestation (format HTML/Print).
     */
    public function show(Attestation $attestation)
    {
        $page_title = 'Aperçu de l\'Attestation';
        $attestation->load(['apprenant', 'formation']);
        
        return view('admin.attestations.show', compact('attestation', 'page_title'));
    }

    /**
     * Supprimer une attestation.
     */
    public function destroy(Attestation $attestation)
    {
        $attestation->delete();
        return redirect()->route('admin.attestations.index')->with('success', 'Attestation supprimée.');
    }
}
