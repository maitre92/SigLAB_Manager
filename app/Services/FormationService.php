<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Formation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class FormationService
{
    public function getAllPaginated(
        ?string $search = null,
        ?string $categorieId = null,
        ?string $type = null,
        ?string $statut = null,
        ?int $formateurId = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        return Formation::query()
            ->with(['categorie', 'formateurs'])
            ->search($search)
            ->byCategorie($categorieId)
            ->byType($type)
            ->byStatut($statut)
            ->when($formateurId, fn($query) => $query->whereHas('formateurs', fn($q) => $q->where('users.id', $formateurId)))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Formation
    {
        $formateurIds = Arr::pull($data, 'formateurs', []);
        $formation = Formation::create($data);
        $this->syncFormateurs($formation, $formateurIds);

        ActivityLog::log(
            action: 'create_formation',
            subject: 'Formation',
            subjectId: $formation->id,
            description: "La formation {$formation->nom} ({$formation->code}) a été créée"
        );

        return $formation->load(['categorie', 'formateurs']);
    }

    public function update(Formation $formation, array $data): bool
    {
        $formateurIds = Arr::pull($data, 'formateurs', []);
        $changes = [];

        foreach ($data as $key => $value) {
            if ($formation->getRawOriginal($key) != $value) {
                $changes[$key] = [
                    'old' => $formation->getRawOriginal($key),
                    'new' => $value,
                ];
            }
        }

        $result = $formation->update($data);
        $this->syncFormateurs($formation, $formateurIds);

        if ($result) {
            ActivityLog::log(
                action: 'update_formation',
                subject: 'Formation',
                subjectId: $formation->id,
                description: "La formation {$formation->nom} ({$formation->code}) a été modifiée",
                changes: $changes
            );
        }

        return $result;
    }

    public function delete(Formation $formation): bool
    {
        $result = $formation->delete();

        if ($result) {
            ActivityLog::log(
                action: 'delete_formation',
                subject: 'Formation',
                subjectId: $formation->id,
                description: "La formation {$formation->nom} ({$formation->code}) a été supprimée"
            );
        }

        return $result;
    }

    private function syncFormateurs(Formation $formation, ?array $formateurIds): void
    {
        $syncData = collect($formateurIds ?? [])
            ->filter()
            ->unique()
            ->mapWithKeys(fn($id) => [$id => ['role' => 'formateur', 'assigned_at' => now()]])
            ->toArray();

        $formation->formateurs()->sync($syncData);
    }
}
