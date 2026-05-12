<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Apprenant;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

/**
 * Service pour la gestion des apprenants
 */
class ApprenantService
{
    /**
     * Créer un nouvel apprenant
     */
    public function create(array $data): Apprenant
    {
        // Gestion de l'upload de photo
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        $apprenant = Apprenant::create($data);

        ActivityLog::log(
            action: 'create_apprenant',
            subject: 'Apprenant',
            subjectId: $apprenant->id,
            description: "L'apprenant {$apprenant->nom_complet} ({$apprenant->matricule}) a été créé"
        );

        return $apprenant;
    }

    /**
     * Mettre à jour un apprenant
     */
    public function update(Apprenant $apprenant, array $data): bool
    {
        // Gestion de l'upload de photo
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            // Supprimer l'ancienne photo
            $this->deletePhoto($apprenant);
            $data['photo'] = $this->uploadPhoto($data['photo']);
        } else {
            // Ne pas écraser la photo si aucun nouveau fichier n'est uploadé
            unset($data['photo']);
        }

        // Supprimer la photo si demandé
        if (isset($data['remove_photo']) && $data['remove_photo']) {
            $this->deletePhoto($apprenant);
            $data['photo'] = null;
            unset($data['remove_photo']);
        }

        // Tracker les modifications
        $changes = [];
        foreach ($data as $key => $value) {
            $oldValue = $apprenant->getRawOriginal($key);
            if ($oldValue != $value) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $value,
                ];
            }
        }

        $result = $apprenant->update($data);

        if ($result && !empty($changes)) {
            ActivityLog::log(
                action: 'update_apprenant',
                subject: 'Apprenant',
                subjectId: $apprenant->id,
                description: "L'apprenant {$apprenant->nom_complet} ({$apprenant->matricule}) a été modifié",
                changes: $changes
            );
        }

        return $result;
    }

    /**
     * Supprimer un apprenant (soft delete)
     */
    public function delete(Apprenant $apprenant): bool
    {
        $result = $apprenant->delete();

        if ($result) {
            ActivityLog::log(
                action: 'delete_apprenant',
                subject: 'Apprenant',
                subjectId: $apprenant->id,
                description: "L'apprenant {$apprenant->nom_complet} ({$apprenant->matricule}) a été supprimé"
            );
        }

        return $result;
    }

    /**
     * Restaurer un apprenant supprimé
     */
    public function restore(Apprenant $apprenant): bool
    {
        $result = $apprenant->restore();

        if ($result) {
            ActivityLog::log(
                action: 'restore_apprenant',
                subject: 'Apprenant',
                subjectId: $apprenant->id,
                description: "L'apprenant {$apprenant->nom_complet} ({$apprenant->matricule}) a été restauré"
            );
        }

        return $result;
    }

    /**
     * Récupérer les apprenants avec pagination et filtres
     */
    public function getAllPaginated(
        ?string $search = null,
        ?string $statut = null,
        ?string $niveauEtude = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Apprenant::query();

        if ($search) {
            $query->search($search);
        }

        if ($statut) {
            $query->byStatut($statut);
        }

        if ($niveauEtude) {
            $query->byNiveauEtude($niveauEtude);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Trouver un apprenant par ID
     */
    public function find(int $id): ?Apprenant
    {
        return Apprenant::find($id);
    }

    /**
     * Trouver un apprenant par matricule
     */
    public function findByMatricule(string $matricule): ?Apprenant
    {
        return Apprenant::where('matricule', $matricule)->first();
    }

    /**
     * Upload d'une photo
     */
    public function uploadPhoto(UploadedFile $file): string
    {
        $filename = 'apprenant_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('apprenants/photos', $filename, 'public');
        return $path;
    }

    /**
     * Supprimer la photo d'un apprenant
     */
    public function deletePhoto(Apprenant $apprenant): void
    {
        if ($apprenant->photo && Storage::disk('public')->exists($apprenant->photo)) {
            Storage::disk('public')->delete($apprenant->photo);
        }
    }

    /**
     * Compter les apprenants
     */
    public function count(): int
    {
        return Apprenant::count();
    }

    /**
     * Compter les apprenants actifs
     */
    public function countActive(): int
    {
        return Apprenant::active()->count();
    }

    /**
     * Obtenir des statistiques
     */
    public function getStats(): array
    {
        return [
            'total' => $this->count(),
            'actifs' => $this->countActive(),
            'inactifs' => Apprenant::byStatut('inactif')->count(),
            'diplomes' => Apprenant::byStatut('diplome')->count(),
            'abandonnes' => Apprenant::byStatut('abandonne')->count(),
        ];
    }
}
