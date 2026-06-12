<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use App\Models\Attestation;
use App\Models\Formation;
use App\Models\GroupeFormation;
use App\Models\Inscription;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $attestations = Attestation::with(['apprenant', 'formation', 'groupeFormation'])->latest()->get();
        
        // On récupère les groupes terminés pour proposer la génération
        $groupesFormation = GroupeFormation::with(['formation', 'apprenants'])
            ->withCount('attestations')
            ->where('statut', 'terminee')
            ->get();

        return view('admin.attestations.index', compact('attestations', 'groupesFormation', 'page_title'));
    }

    /**
     * Formulaire pour générer une attestation pour un apprenant spécifique.
     */
    public function create(Request $request)
    {
        $page_title = 'Générer une Attestation';
        $apprenantId = $request->apprenant_id;
        $groupeFormationId = $request->groupe_formation_id;

        $apprenant = Apprenant::findOrFail($apprenantId);
        $groupeFormation = GroupeFormation::with('formation')->findOrFail($groupeFormationId);
        $formation = $groupeFormation->formation;

        return view('admin.attestations.create', compact('apprenant', 'formation', 'groupeFormation', 'page_title'));
    }

    /**
     * Enregistrer l'attestation en base.
     */
    public function store(Request $request)
    {
        $request->validate([
            'apprenant_id' => 'required|exists:apprenants,id',
            'groupe_formation_id' => 'required|exists:groupes_formation,id',
            'date_emission' => 'required|date',
        ]);

        $groupeFormation = GroupeFormation::findOrFail($request->groupe_formation_id);
        $inscription = Inscription::where('apprenant_id', $request->apprenant_id)
            ->where('groupe_formation_id', $groupeFormation->id)
            ->first();

        if (!$inscription) {
            return redirect()->route('admin.attestations.index')->with('error', 'Cet apprenant n\'est pas inscrit à ce groupe de formation.');
        }

        if ($inscription->montant_paye < $inscription->montant_total) {
            $reste = $inscription->montant_total - $inscription->montant_paye;
            return redirect()->route('admin.attestations.index')->with('error', 'La génération de l\'attestation a échoué. Le paiement des frais de formation n\'est pas total (Reste à payer : ' . number_format($reste, 0, ',', ' ') . ' FCFA).');
        }

        // Vérifier si une attestation existe déjà pour ce couple
        $exists = Attestation::where('apprenant_id', $request->apprenant_id)
            ->where('groupe_formation_id', $request->groupe_formation_id)
            ->first();

        if ($exists) {
            return redirect()->route('admin.attestations.index')->with('error', 'Une attestation existe déjà pour cet apprenant sur ce groupe.');
        }

        Attestation::create([
            'reference' => Attestation::generateReference(),
            'apprenant_id' => $request->apprenant_id,
            'formation_id' => $groupeFormation->formation_id,
            'groupe_formation_id' => $groupeFormation->id,
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
        $attestation->load(['apprenant', 'formation', 'groupeFormation']);
        
        return view('admin.attestations.show', compact('attestation', 'page_title'));
    }

    public function downloadPdf(Attestation $attestation)
    {
        $attestation->load(['apprenant', 'formation', 'groupeFormation']);
        $page_title = 'Attestation ' . $attestation->reference;
        $isPdf = true;

        $pdf = Pdf::loadView('admin.attestations.pdf', compact(
            'attestation',
            'page_title',
            'isPdf'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('attestation-' . $attestation->reference . '.pdf');
    }

    public function downloadGroupPdf(GroupeFormation $groupeFormation)
    {
        $groupeFormation->load('formation');

        $attestations = Attestation::with(['apprenant', 'formation', 'groupeFormation'])
            ->where('groupe_formation_id', $groupeFormation->id)
            ->orderBy('reference')
            ->get();

        if ($attestations->isEmpty()) {
            return redirect()->route('admin.attestations.index')
                ->with('error', 'Aucune attestation générée pour ce groupe.');
        }

        $page_title = 'Attestations du groupe ' . $groupeFormation->nom;
        $isPdf = true;

        $pdf = Pdf::loadView('admin.attestations.pdf', compact(
            'attestations',
            'page_title',
            'isPdf'
        ))->setPaper('a4', 'landscape');

        $filename = 'attestations-groupe-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', $groupeFormation->nom) . '.pdf';

        return $pdf->download($filename);
    }

    public function verify(string $reference)
    {
        $attestation = Attestation::with(['apprenant', 'formation', 'groupeFormation'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('attestations.verify', compact('attestation'));
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
