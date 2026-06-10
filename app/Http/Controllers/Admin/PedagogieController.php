<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use App\Models\Evaluation;
use App\Models\Formation;
use App\Models\GroupeFormation;
use App\Models\Note;
use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedagogieController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function presences(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $groupeFormationId = $request->get('groupe_formation_id', $request->get('formation_id'));
        $page_title = 'Feuille de Présence';
        
        $groupesFormation = GroupeFormation::with('formation')
            ->where('statut', 'en_cours')
            ->whereHas('formation', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->get();
        $apprenants = [];
        $presences = [];

        if ($groupeFormationId) {
            $groupeFormation = GroupeFormation::with('apprenants')->findOrFail($groupeFormationId);
            $apprenants = $groupeFormation->apprenants;
            $presences = Presence::where('groupe_formation_id', $groupeFormationId)
                ->where('date', $date)
                ->get()
                ->keyBy('apprenant_id');
        }

        return view('admin.pedagogie.presences', compact('groupesFormation', 'apprenants', 'presences', 'date', 'groupeFormationId', 'page_title'));
    }

    /**
     * Store attendance records.
     */
    public function storePresences(Request $request)
    {
        $request->validate([
            'groupe_formation_id' => 'required|exists:groupes_formation,id',
            'date' => 'required|date',
            'presences' => 'required|array',
        ]);

        $groupeFormation = GroupeFormation::findOrFail($request->groupe_formation_id);
        $date = $request->date;

        DB::transaction(function () use ($request, $groupeFormation, $date) {
            foreach ($request->presences as $apprenantId => $statut) {
                Presence::updateOrCreate(
                    [
                        'groupe_formation_id' => $groupeFormation->id,
                        'apprenant_id' => $apprenantId,
                        'date' => $date
                    ],
                    [
                        'formation_id' => $groupeFormation->formation_id,
                        'statut' => $statut,
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'Présences enregistrées avec succès.');
    }

    /**
     * Display evaluations.
     */
    public function evaluations()
    {
        $page_title = 'Évaluations';
        $evaluations = Evaluation::with(['formation', 'groupeFormation'])
            ->where('type', 'evaluation')
            ->orderBy('date_evaluation', 'desc')
            ->get();
        
        $groupesFormation = GroupeFormation::with('formation')
            ->where('statut', 'en_cours')
            ->whereHas('formation', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->get();

        return view('admin.pedagogie.evaluations', compact('evaluations', 'groupesFormation', 'page_title'));
    }

    /**
     * Display exams.
     */
    public function examens()
    {
        $page_title = 'Examens';
        $examens = Evaluation::with(['formation', 'groupeFormation'])
            ->where('type', 'examen')
            ->orderBy('date_evaluation', 'desc')
            ->get();
        
        $groupesFormation = GroupeFormation::with('formation')
            ->where('statut', 'en_cours')
            ->whereHas('formation', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->get();

        return view('admin.pedagogie.examens', compact('examens', 'groupesFormation', 'page_title'));
    }

    /**
     * Store a new evaluation or exam.
     */
    public function storeEvaluation(Request $request)
    {
        $request->validate([
            'groupe_formation_id' => 'required|exists:groupes_formation,id',
            'titre' => 'required|string|max:255',
            'type' => 'required|in:evaluation,examen',
            'date_evaluation' => 'required|date',
            'coefficient' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $groupeFormation = GroupeFormation::findOrFail($request->groupe_formation_id);

        Evaluation::create($request->only(['groupe_formation_id', 'titre', 'type', 'date_evaluation', 'coefficient', 'description']) + [
            'formation_id' => $groupeFormation->formation_id,
        ]);

        $message = $request->type == 'examen' ? 'Examen programmé avec succès.' : 'Évaluation créée avec succès.';
        return redirect()->back()->with('success', $message);
    }

    /**
     * Display a list of evaluations to enter grades.
     */
    public function notes()
    {
        $page_title = 'Gestion des Notes';
        $evaluations = Evaluation::with(['formation', 'groupeFormation', 'notes'])
            ->orderBy('date_evaluation', 'desc')
            ->get();

        return view('admin.pedagogie.notes', compact('evaluations', 'page_title'));
    }

    /**
     * Show form to edit notes for a specific evaluation.
     */
    public function editNotes(Evaluation $evaluation)
    {
        $page_title = 'Saisie des Notes';
        $evaluation->load('groupeFormation.apprenants', 'formation.apprenants', 'notes');
        $apprenants = $evaluation->groupeFormation?->apprenants ?? ($evaluation->formation?->apprenants ?? collect());
        $notes = $evaluation->notes->keyBy('apprenant_id');

        return view('admin.pedagogie.edit_notes', compact('evaluation', 'apprenants', 'notes', 'page_title'));
    }

    /**
     * Store notes for an evaluation.
     */
    public function storeNotes(Request $request, Evaluation $evaluation)
    {
        $request->validate([
            'notes' => 'required|array',
            'notes.*' => 'nullable|numeric|min:0|max:20',
            'commentaires' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $evaluation) {
            foreach ($request->notes as $apprenantId => $valeur) {
                if ($valeur !== null) {
                    Note::updateOrCreate(
                        [
                            'evaluation_id' => $evaluation->id,
                            'apprenant_id' => $apprenantId
                        ],
                        [
                            'groupe_formation_id' => $evaluation->groupe_formation_id,
                            'valeur' => $valeur,
                            'commentaire' => $request->commentaires[$apprenantId] ?? null
                        ]
                    );
                }
            }
        });

        return redirect()->route('admin.pedagogie.notes')->with('success', 'Notes enregistrées avec succès.');
    }

    /**
     * Display a summary of results for a formation.
     */
    public function resultats(Request $request)
    {
        $page_title = 'Résultats des Examens & Évaluations';
        $groupeFormationId = $request->get('groupe_formation_id', $request->get('formation_id'));
        $groupesFormation = GroupeFormation::with('formation')
            ->whereHas('formation', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->get();
        
        $apprenants = [];
        $evaluations = [];
        $notes = [];
        $groupeFormation = null;

        if ($groupeFormationId) {
            $groupeFormation = GroupeFormation::with(['formation', 'apprenants', 'evaluations.notes'])->findOrFail($groupeFormationId);
            $apprenants = $groupeFormation->apprenants;
            $evaluations = $groupeFormation->evaluations()->orderBy('date_evaluation', 'asc')->get();
            
            // Re-organize notes for easier access in view: $notes[apprenant_id][evaluation_id]
            foreach ($evaluations as $eval) {
                foreach ($eval->notes as $note) {
                    $notes[$note->apprenant_id][$eval->id] = $note->valeur;
                }
            }
        }

        return view('admin.pedagogie.resultats', compact('groupesFormation', 'groupeFormation', 'apprenants', 'evaluations', 'notes', 'page_title', 'groupeFormationId'));
    }
}
