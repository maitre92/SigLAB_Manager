# sigLAB Manager

Application Laravel de gestion administrative, pédagogique et financière pour un centre de formation.

## Présentation

**sigLAB Manager** permet de gérer le cycle complet d'une formation: utilisateurs, apprenants, formations, groupes de formation, inscriptions, présences, évaluations, paiements, dépenses, commissions formateurs et attestations.

Le projet est construit avec Laravel et utilise une interface d'administration responsive.

## Fonctionnalités principales

- Tableau de bord administrateur avec statistiques.
- Gestion des utilisateurs, rôles et permissions.
- Gestion des apprenants et de leurs inscriptions.
- Gestion des formations et catégories de formation.
- Gestion des groupes de formation avec formateurs, planning, salle, capacité et statut.
- Génération PDF de l'emploi du temps d'un groupe de formation.
- Gestion pédagogique: présences, évaluations, examens, notes et résultats.
- Gestion financière: paiements apprenants, reçus, dépenses et paiements/commissions formateurs.
- Gestion des attestations par groupe de formation.
- Contrôle du paiement complet avant génération d'une attestation.

## Modules importants

### Formations et groupes

Les formations représentent l'offre pédagogique globale. Les opérations concrètes se font ensuite par **groupes de formation**:

- création et modification des groupes;
- attribution du formateur principal et des formateurs associés;
- suivi du statut: planifiée, en cours, terminée ou suspendue;
- liaison des inscriptions, présences, évaluations, notes, dépenses et attestations au groupe.

Routes principales:

```text
admin/formations
admin/groupes-formations
admin/groupes-formations/{groupe}/emploi-du-temps/pdf
```

### Apprenants

Le module apprenants permet de créer, modifier et consulter les dossiers des apprenants. Les inscriptions relient chaque apprenant à une formation et à un groupe de formation.

Route principale:

```text
admin/apprenants
```

### Pédagogie

Le suivi pédagogique est basé sur les groupes de formation:

- feuille de présence;
- création des évaluations et examens;
- saisie des notes;
- consultation des résultats.

Routes principales:

```text
admin/pedagogie/presences
admin/pedagogie/evaluations
admin/pedagogie/examens
admin/pedagogie/notes
admin/pedagogie/resultats
```

### Finances

Le module finances couvre:

- encaissements des paiements apprenants;
- génération de reçus;
- suivi des dépenses;
- paiement des formateurs et commissions.

Routes principales:

```text
admin/finances
admin/finances/paiements
admin/finances/depenses
admin/finances/formateurs
```

### Attestations

Les attestations sont générées pour un apprenant inscrit à un groupe de formation terminé. La génération vérifie aussi que les frais de formation sont totalement payés.

Route principale:

```text
admin/attestations
```

## Installation

### Prérequis

- PHP compatible avec la version Laravel du projet.
- Composer.
- MySQL ou MariaDB.
- Node.js et npm si vous souhaitez compiler les assets.

### Mise en place

```bash
cd /opt/lampp/htdocs/sigLAB_Manager
composer install
cp .env.example .env
php artisan key:generate
```

Configurer ensuite la base de données dans `.env`:

```env
APP_NAME="sigLAB Manager"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siglab_manager
DB_USERNAME=root
DB_PASSWORD=
```

Lancer les migrations et les seeders:

```bash
php artisan migrate --seed
```

Démarrer le serveur local:

```bash
php artisan serve
```

L'application sera disponible sur:

```text
http://127.0.0.1:8000
```

## Commandes utiles

Nettoyer les caches Laravel:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

Relancer les migrations en développement:

```bash
php artisan migrate:fresh --seed
```

Lancer les tests:

```bash
php artisan test
```

Si les tests utilisent SQLite en mémoire, vérifier que l'extension PHP SQLite est installée et activée.

## Structure du projet

```text
app/
├── Http/Controllers/Admin/      Contrôleurs des modules admin
├── Http/Requests/               Validation des formulaires
├── Models/                      Modèles Eloquent
└── Services/                    Services applicatifs

database/
├── migrations/                  Structure de la base de données
└── seeders/                     Données initiales

resources/views/
├── admin/                       Interfaces d'administration
├── auth/                        Authentification et profil
└── layouts/                     Layouts, sidebar et navigation

routes/
└── web.php                      Routes web de l'application
```

## Mises à jour récentes

### Formation par groupe

Ajout du module **groupes de formation**:

- nouveau contrôleur `GroupeFormationController`;
- nouveau modèle `GroupeFormation`;
- nouvelles vues dans `resources/views/admin/groupes-formations`;
- nouvelles migrations pour créer les groupes et rattacher les opérations au champ `groupe_formation_id`;
- adaptation des inscriptions, présences, évaluations, notes, paiements, dépenses et attestations aux groupes de formation.

### Attestations

La génération d'attestation est maintenant liée au groupe de formation et vérifie que l'apprenant a entièrement payé sa formation avant création.

### Pédagogie

Les présences, évaluations, examens, notes et résultats s'appuient maintenant sur les groupes de formation afin de mieux suivre chaque cohorte.

## Git

Après un `git pull` avec rebase, résoudre les conflits puis continuer:

```bash
git status
git add <fichiers_resolus>
git rebase --continue
```

Pour envoyer les travaux:

```bash
git push origin main
```

## Licence

Projet propriétaire - sigLAB.
