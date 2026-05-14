<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Depense;
use App\Models\Inscription;
use App\Models\Apprenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index()
    {
        $total_revenue = Paiement::sum('montant');
        $total_expenses = Depense::sum('montant');
        $balance = $total_revenue - $total_expenses;
        
        $recent_paiements = Paiement::with(['inscription.apprenant', 'inscription.formation'])
            ->latest()
            ->take(10)
            ->get();
            
        $recent_depenses = Depense::latest()->take(10)->get();
        
        // Data for charts
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('M'));
        $revenue_by_month = collect(range(5, 0))->map(fn($i) => 
            Paiement::whereYear('date_paiement', now()->subMonths($i)->year)
                   ->whereMonth('date_paiement', now()->subMonths($i)->month)
                   ->sum('montant')
        );
        $expenses_by_month = collect(range(5, 0))->map(fn($i) => 
            Depense::whereYear('date_depense', now()->subMonths($i)->year)
                   ->whereMonth('date_depense', now()->subMonths($i)->month)
                   ->sum('montant')
        );

        return view('admin.finances.index', compact(
            'total_revenue', 'total_expenses', 'balance', 
            'recent_paiements', 'recent_depenses',
            'months', 'revenue_by_month', 'expenses_by_month'
        ));
    }

    public function payments()
    {
        $paiements = Paiement::with(['inscription.apprenant', 'inscription.formation'])->latest()->paginate(20);
        $inscriptions = Inscription::with(['apprenant', 'formation'])
            ->where('statut', '!=', 'terminee')
            ->get();
            
        return view('admin.finances.payments', compact('paiements', 'inscriptions'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'montant' => 'required|numeric|min:1',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $paiement = Paiement::create([
                'inscription_id' => $request->inscription_id,
                'montant' => $request->montant,
                'date_paiement' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'reference' => $request->reference,
                'recu_numero' => Paiement::generateRecuNumero(),
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // Update inscription paid amount
            $inscription = Inscription::find($request->inscription_id);
            $inscription->increment('montant_paye', $request->montant);
        });

        return redirect()->back()->with('success', 'Paiement enregistré avec succès.');
    }

    public function expenses()
    {
        $depenses = Depense::latest()->paginate(20);
        $categories = Depense::getCategories();
        return view('admin.finances.expenses', compact('depenses', 'categories'));
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'categorie' => 'required|string',
            'montant' => 'required|numeric|min:1',
            'date_depense' => 'required|date',
            'beneficiaire' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Depense::create(array_merge($request->all(), [
            'created_by' => Auth::id()
        ]));

        return redirect()->back()->with('success', 'Dépense enregistrée avec succès.');
    }

    public function receipt(Paiement $paiement)
    {
        $paiement->load(['inscription.apprenant', 'inscription.formation', 'creator']);
        return view('admin.finances.receipt', compact('paiement'));
    }
}
