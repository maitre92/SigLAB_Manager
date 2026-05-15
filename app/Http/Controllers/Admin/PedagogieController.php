<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use App\Models\Evaluation;
use App\Models\Formation;
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
        $formationId = $request->get('formation_id');
        $page_title = 'Feuille de Présence';
        
        $formations = Formation::where('statut', 'en_cours')->get();
        $apprenants = [];
        $presences = [];

        if ($formationId) {
            $formation = Formation::with('apprenants')->findOrFail($formationId);
            $apprenants = $formation->apprenants;
            $presences = Presence::where('formation_id', $formationId)
                ->where('date', $date)
                ->get()
                ->keyBy('apprenant_id');
        }

        return view('admin.pedagogie.presences', compact('formations', 'apprenants', 'presences', 'date', 'formationId', 'page_title'));
    }

    /**
     * Store attendance records.
     */
    public function storePresences(Request $request)
    {
        $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'date' => 'required|date',
            'presences' => 'required|array',
        ]);

        $formationId = $request->formation_id;
        $date = $request->date;

        DB::transaction(function () use ($request, $formationId, $date) {
            foreach ($request->presences as $apprenantId => $statut) {
                Presence::updateOrCreate(
                    [
                        'formation_id' => $formationId,
                        'apprenant_id' => $apprenantId,
                        'date' => $date
                    ],
                    ['statut' => $statut]
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
        $evaluations = Evaluation::with('formation')
            ->where('type', 'evaluation')
            ->orderBy('date_evaluation', 'desc')
            ->get();
        
        $formations = Formation::where('statut', 'en_cours')->get();

        return view('admin.pedagogie.evaluations', compact('evaluations', 'formations', 'page_title'));
    }

    /**
     * Display exams.
     */
    public function examens()
    {
        $page_title = 'Examens';
        $examens = Evaluation::with('formation')
            ->where('type', 'examen')
            ->orderBy('date_evaluation', 'desc')
            ->get();
        
        $formations = Formation::where('statut', 'en_cours')->get();

        return view('admin.pedagogie.examens', compact('examens', 'formations', 'page_title'));
    }

    /**
     * Store a new evaluation or exam.
     */
    public function storeEvaluation(Request $request)
    {
        $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'titre' => 'required|string|max:255',
            'type' => 'required|in:evaluation,examen',
            'date_evaluation' => 'required|date',
            'coefficient' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Evaluation::create($request->all());

        $message = $request->type == 'examen' ? 'Examen programmé avec succès.' : 'Évaluation créée avec succès.';
        return redirect()->back()->with('success', $message);
    }

    /**
     * Display a list of evaluations to enter grades.
     */
    public function notes()
    {
        $page_title = 'Gestion des Notes';
        $evaluations = Evaluation::with(['formation', 'notes'])
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
        $evaluation->load('formation.apprenants', 'notes');
        $apprenants = $evaluation->formation->apprenants;
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
        $formationId = $request->get('formation_id');
        $formations = Formation::all();
        
        $apprenants = [];
        $evaluations = [];
        $notes = [];
        $formation = null;

        if ($formationId) {
            $formation = Formation::with(['apprenants', 'evaluations.notes'])->findOrFail($formationId);
            $apprenants = $formation->apprenants;
            $evaluations = $formation->evaluations()->orderBy('date_evaluation', 'asc')->get();
            
            // Re-organize notes for easier access in view: $notes[apprenant_id][evaluation_id]
            foreach ($evaluations as $eval) {
                foreach ($eval->notes as $note) {
                    $notes[$note->apprenant_id][$eval->id] = $note->valeur;
                }
            }
        }

        return view('admin.pedagogie.resultats', compact('formations', 'formation', 'apprenants', 'evaluations', 'notes', 'page_title', 'formationId'));
    }
}
