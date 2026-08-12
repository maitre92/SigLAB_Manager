<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prestation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrestationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_prestations')->only(['index', 'receipt']);
        $this->middleware('permission:create_prestation')->only(['store']);
        $this->middleware('permission:edit_prestation')->only(['update']);
        $this->middleware('permission:delete_prestation')->only(['destroy']);
    }

    /**
     * Affiche la page principale des prestations avec le formulaire et la liste
     */
    public function index(Request $request)
    {
        $query = Prestation::with('creator');

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('client_nom', 'like', "%{$search}%")
                  ->orWhere('client_telephone', 'like', "%{$search}%")
                  ->orWhere('recu_numero', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par période
        if ($request->filled('date_debut')) {
            $query->whereDate('date_prestation', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_prestation', '<=', $request->date_fin);
        }

        $prestations = $query->latest('date_prestation')->paginate(20)->withQueryString();

        // Statistiques de la caisse prestations
        $total_prestations = Prestation::sum('montant');
        $total_mois = Prestation::whereYear('date_prestation', now()->year)
            ->whereMonth('date_prestation', now()->month)
            ->sum('montant');
        $count_prestations = Prestation::count();
        $count_mois = Prestation::whereYear('date_prestation', now()->year)
            ->whereMonth('date_prestation', now()->month)
            ->count();

        return view('admin.prestations.index', compact(
            'prestations', 'total_prestations', 'total_mois', 
            'count_prestations', 'count_mois'
        ));
    }

    /**
     * Enregistre une nouvelle prestation
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'client_nom' => 'required|string|max:255',
            'client_telephone' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:2000',
            'montant' => 'required|numeric|min:1',
            'date_prestation' => 'required|date|before_or_equal:today',
            'mode_paiement' => 'required|string',
            'reference' => 'nullable|string|max:255',
            'statut' => 'required|string|in:en_cours,terminee,livree',
            'notes' => 'nullable|string|max:2000',
        ]);

        Prestation::create([
            'titre' => $request->titre,
            'client_nom' => $request->client_nom,
            'client_telephone' => $request->client_telephone,
            'description' => $request->description,
            'montant' => $request->montant,
            'date_prestation' => $request->date_prestation,
            'mode_paiement' => $request->mode_paiement,
            'reference' => $request->reference,
            'recu_numero' => Prestation::generateRecuNumero(),
            'statut' => $request->statut,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Prestation enregistrée avec succès.');
    }

    /**
     * Met à jour une prestation existante
     */
    public function update(Request $request, Prestation $prestation)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'client_nom' => 'required|string|max:255',
            'client_telephone' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:2000',
            'montant' => 'required|numeric|min:1',
            'date_prestation' => 'required|date|before_or_equal:today',
            'mode_paiement' => 'required|string',
            'reference' => 'nullable|string|max:255',
            'statut' => 'required|string|in:en_cours,terminee,livree',
            'notes' => 'nullable|string|max:2000',
        ]);

        $prestation->update($request->only([
            'titre', 'client_nom', 'client_telephone', 'description',
            'montant', 'date_prestation', 'mode_paiement', 'reference',
            'statut', 'notes'
        ]));

        return redirect()->back()->with('success', 'Prestation modifiée avec succès.');
    }

    /**
     * Supprime une prestation
     */
    public function destroy(Prestation $prestation)
    {
        $prestation->delete();

        return redirect()->back()->with('success', 'Prestation supprimée avec succès.');
    }

    /**
     * Affiche le reçu de prestation (pour impression)
     */
    public function receipt(Prestation $prestation)
    {
        $prestation->load('creator');
        return view('admin.prestations.receipt', compact('prestation'));
    }
}
