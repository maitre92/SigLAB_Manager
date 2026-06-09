<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategorieFormation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategorieFormationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:voir_categories_formations,ajouter_categorie_formation,modifier_categorie_formation,supprimer_categorie_formation,gerer_categories_formations,voir_formations')->only('index');
        $this->middleware('permission:ajouter_categorie_formation,gerer_categories_formations')->only('store');
        $this->middleware('permission:modifier_categorie_formation,gerer_categories_formations')->only('update');
        $this->middleware('permission:supprimer_categorie_formation,gerer_categories_formations')->only(['destroy', 'restore']);
    }

    public function index()
    {
        $categories = CategorieFormation::withCount('formations')->orderBy('nom')->get();
        $archivedCategories = CategorieFormation::onlyTrashed()->withCount('formations')->orderBy('nom')->get();

        return view('admin.categories_formations.index', compact('categories', 'archivedCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($request->nom);
        $existingCategory = CategorieFormation::withTrashed()
            ->where('nom', $request->nom)
            ->orWhere('slug', $slug)
            ->first();

        if ($existingCategory && ! $existingCategory->trashed()) {
            return redirect()->back()
                ->withErrors(['nom' => 'Une catégorie active porte déjà ce nom.'])
                ->withInput();
        }

        if ($existingCategory && $existingCategory->trashed()) {
            $existingCategory->restore();
            $existingCategory->update([
                'nom' => $request->nom,
                'slug' => $slug,
                'description' => $request->description,
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', 'Catégorie restaurée avec succès.');
        }

        CategorieFormation::create([
            'nom' => $request->nom,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Catégorie créée avec succès.');
    }

    public function update(Request $request, CategorieFormation $categorieFormation)
    {
        $request->validate([
            'nom' => 'required|unique:categorie_formations,nom,' . $categorieFormation->id,
            'description' => 'nullable|string',
        ]);

        $categorieFormation->update([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(CategorieFormation $categorieFormation)
    {
        $formationsCount = $categorieFormation->formations()->count();

        if ($formationsCount === 0) {
            $categorieFormation->forceDelete();

            return redirect()->back()->with('success', 'Catégorie supprimée définitivement avec succès.');
        }

        $categorieFormation->delete();

        return redirect()->back()->with('success', "Catégorie archivée avec succès. {$formationsCount} formation(s) restent conservée(s).");
    }

    public function restore($categorieFormation)
    {
        $category = CategorieFormation::onlyTrashed()->findOrFail($categorieFormation);
        $category->restore();

        return redirect()->back()->with('success', 'Catégorie restaurée avec succès.');
    }
}
