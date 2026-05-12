# sigLAB_Manager

Plateforme de gestion professionnelle et modulaire basée sur Laravel.

## 📋 Table des matières
- [Présentation](#présentation)
- [Architecture](#architecture)
- [Installation](#installation)
- [Structure du projet](#structure-du-projet)
- [Guide de développement](#guide-de-développement)
- [Configuration](#configuration)

## 🎯 Présentation

**sigLAB_Manager** est une application web d'administration et de gestion modulable, construite avec Laravel 13.7 et PHP 8.3. Elle fournit une base solide et extensible pour des projets d'entreprise.

### Caractéristiques
- ✅ Architecture clean et modulaire
- ✅ Système d'authentification sécurisé
- ✅ Gestion des rôles et permissions
- ✅ Interface admin responsive
- ✅ API RESTful
- ✅ Système de logging avancé
- ✅ Tests intégrés

## 🏗️ Architecture

L'application suit une architecture en couches avec les principes SOLID :

```
app/
├── Core/                    # Logique métier fondamentale
│   ├── Domain/             # Entités métier
│   ├── Infrastructure/     # Services externes
│   └── Support/            # Utilitaires
├── Features/               # Domaines métier isolés
│   ├── Admin/
│   ├── Users/
│   ├── Dashboard/
│   └── Reports/
├── Shared/                 # Code réutilisable
│   ├── DTOs/              # Data Transfer Objects
│   ├── Enums/             # Énumérations
│   ├── Helpers/           # Fonctions utilitaires
│   └── Traits/            # Traits réutilisables
├── Http/                  # Couche HTTP
│   ├── Controllers/       # Contrôleurs organisés par feature
│   ├── Middleware/        # Middlewares
│   ├── Requests/          # Form Requests validées
│   └── Resources/         # API Resources
├── Models/                # Modèles Eloquent
├── Repositories/          # Pattern Repository
├── Services/              # Logique applicative
├── Policies/              # Policies d'autorisation
└── [Events, Jobs, Mail...] # Divers
```

## 🚀 Installation

### Prérequis
- PHP 8.3+
- Composer
- MySQL/MariaDB
- Node.js & npm (optionnel)

### Étapes d'installation

1. **Cloner ou accéder au projet**
```bash
cd /opt/lampp/htdocs/sigLAB_Manager
```

2. **Installer les dépendances PHP**
```bash
composer install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurer la base de données**
Éditer le fichier `.env` avec vos identifiants DB, puis :
```bash
php artisan migrate
php artisan seed:run
```

5. **Installer les assets (optionnel)**
```bash
npm install
npm run dev
```

6. **Démarrer le serveur**
```bash
php artisan serve
```

L'application est accessible à `http://localhost:8000`

## 📁 Structure du projet

### Répertoires principaux

- **app/** - Logique applicative
- **config/** - Fichiers de configuration
- **database/** - Migrations et seeders
- **resources/** - Vues, CSS, JS
- **routes/** - Définition des routes
- **storage/** - Fichiers générés
- **tests/** - Tests automatisés
- **public/template-admin/** - Template admin intégré

### Fichiers importants

- `.env` - Variables d'environnement
- `app.php` - Configuration générale
- `auth.php` - Configuration d'authentification
- `modules/` - Configuration des modules métier

## 📖 Guide de développement

### Créer une nouvelle feature

1. Créer un dossier dans `app/Features/{NomFeature}/`
2. Organiser le code : Controllers, Services, Models
3. Ajouter les routes dans `routes/web.php` ou `routes/api.php`
4. Créer les vues correspondantes

### Exemple de structure d'une feature
```
Features/Products/
├── Controllers/ProductController.php
├── Services/ProductService.php
├── Models/Product.php
├── Requests/CreateProductRequest.php
└── Views/products/
```

### Bonnes pratiques

1. **Validation** - Utiliser les Form Requests
2. **Autorisation** - Implémenter les Policies
3. **Services** - Isoler la logique métier
4. **Events** - Découpler les composants
5. **Tests** - Couvrir les cas critiques

## ⚙️ Configuration

### Variables d'environnement essentielles

```env
APP_NAME=sigLAB_Manager
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siglab_manager
DB_USERNAME=root
DB_PASSWORD=

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

### Configuration des modules

Créer des fichiers de configuration dans `config/modules/` pour organiser les paramètres métier.

## 🔐 Sécurité

- Authentification Laravel par défaut
- Middleware CSRF activé
- Validation rigoureuse des inputs
- Protection des APIs avec tokens
- Logging des accès sensibles

## 📝 Licence

Propriétaire - sigLAB

## 👥 Support

Pour toute question ou problème, consulter la documentation Laravel officielle ou contactez l'équipe de développement.

## 🛠️ Mises à jour récentes et guide d'implémentation (mai 2026)

Cette section décrit les modifications que j'ai appliquées récemment, les fichiers touchés, les vérifications à effectuer et les commandes utiles pour tester ou revenir en arrière.

- Objectif : corriger l'authentification, remplacer SQLite par MySQL (configuration), et adapter l'interface (sidebar, paramètres, navbar responsive & thèmes).

Modifications principales effectuées
- Auth
	- Fichier: `app/Services/AuthService.php`
		- Ajout de logs pour diagnostiquer les échecs de connexion.
		- Correction de la comparaison du statut utilisateur (utilisation de l'attribut string de l'énum `UserStatus`).
	- Fichier: `app/Models/User.php`
		- Ajout des méthodes `recordLogin()` et `recordLogout()` pour suivre les connexions.
		- Ajout du trait `HasPermissions` et méthode `isSuperAdmin()`.

- Base de données
	- Fichier: `config/database.php`
		- Suppression du bloc `sqlite` et changement du `default` vers `mysql`.
	- Vérifier et compléter `.env` avec vos identifiants MySQL (`DB_CONNECTION=mysql`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

- Interface (UI)
	- Fichiers modifiés:
		- `resources/views/layouts/sidebar.blade.php` — suppression des modules Document et Rapport ; ajout du menu Paramètres (sous-menus : Liste Utilisateurs, Liste Permissions, Assigner Permissions, Changer mot de passe).
		- `resources/views/admin/settings.blade.php` — refonte en deux colonnes (col-3 menu, col-9 contenu) avec panneaux utilisateurs/permissions/assignation/changement mot de passe et prise en charge du paramètre `?tab=` pour ouvrir un panneau directement.
		- `resources/views/layouts/app.blade.php` & `resources/views/layouts/navbar.blade.php` — navbar configurée via variables CSS (`--navbar-bg`, `--navbar-text`), blanche par défaut et qui prend la couleur du thème choisi; icône menu améliorée pour mobile.

Commandes utiles (appliquer / tester)
1. Assurez-vous que `.env` contient vos identifiants MySQL, puis exécutez :
```bash
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan migrate --seed
```

2. Tester l'authentification (Tinker) :
```bash
php artisan tinker
echo (app(\\App\\Services\\AuthService::class)->login('barrymoustapha485@gmail.com','superadmin123')) ? 'OK' : 'FAIL';
```

3. Tester l'UI :
 - Démarrer le serveur : `php artisan serve`
 - Ouvrir `http://127.0.0.1:8000` ou l'URL retournée
 - Vérifier :
	 - Le menu burger (en mobile) affiche le sidebar.
	 - Le sidebar n'affiche plus Document / Rapports.
	 - Cliquer Paramètres → sous-menu (ou ouvrir `route('admin.settings')?tab=permissions-list`)
	 - Changer thème via le sélecteur (navbar change de couleur)

Points d'attention et rollback
- Sessions : la configuration originale utilisait `database` (table `sessions`) ; si vous voyez des erreurs PDO « could not find driver (sqlite) », vérifiez `SESSION_DRIVER` et la configuration DB. Pour développement local, vous pouvez définir dans `.env` : `SESSION_DRIVER=file`.
- Pour revenir à SQLite : restaurer `config/database.php` depuis votre contrôle de version (git) ou ré-introduire le bloc `sqlite` manuellement.

Fichiers modifiés (rapide)
- `app/Services/AuthService.php`
- `app/Models/User.php`
- `config/database.php`
- `resources/views/layouts/sidebar.blade.php`
- `resources/views/admin/settings.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/navbar.blade.php`

Statut actuel
- Auth: testé en Tinker — connexion `OK` pour l'utilisateur seedé.
- DB: `config` basculé vers MySQL — mettez à jour `.env` et exécutez les migrations.
- UI: sidebar & paramètres mis à jour ; navbar blanche par défaut et responsive.

## État fonctionnel actuel pour les développeurs

Cette application est organisée autour d'une interface admin Laravel classique :

- `routes/web.php` contient les routes publiques, auth, profil et admin.
- `app/Http/Controllers/Auth/AuthController.php` gère connexion, déconnexion, profil personnel, changement et réinitialisation de mot de passe.
- `app/Http/Controllers/Admin/SettingsController.php` centralise l'écran Paramètres : utilisateurs, permissions et assignation des permissions.
- `app/Http/Controllers/Admin/UserController.php` gère aussi la ressource admin users.
- `app/Models/User.php` utilise `HasPermissions` pour vérifier les permissions actives.
- `app/Models/Permission.php` représente les permissions assignables aux utilisateurs.
- `app/Shared/Enums/UserRole.php` porte la hiérarchie des rôles et les rôles assignables/visibles.

### Authentification et profil

- La page de login se trouve dans `resources/views/auth/login.blade.php`.
- Le login affiche maintenant les erreurs de validation et les erreurs d'identifiants via `session('error')`.
- Le message de succès après déconnexion a été supprimé pour éviter une alerte inutile sur l'écran login.
- Le lien secondaire du login pointe vers `Mot de passe oublié ?`.
- Le flux mot de passe oublié utilise les routes :
  - `GET /forgot-password` (`password.request`)
  - `POST /forgot-password` (`password.email`)
  - `GET /reset-password/{token}` (`password.reset`)
  - `POST /reset-password` (`password.store`)
- La page profil unique est `resources/views/auth/profile.blade.php`.
  - Onglet 1 : informations personnelles (`name`, `email`, `phone`)
  - Onglet 2 : changement de mot de passe

### Paramètres, utilisateurs et rôles

- L'écran principal est `resources/views/admin/settings.blade.php`.
- Les onglets sont pilotés par le paramètre `?tab=` :
  - `users-list`
  - `permissions-list`
  - `permissions-assign`
- La hiérarchie des rôles est définie dans `UserRole::level()`.
- Un rôle non-superadmin ne voit et ne gère que les rôles de niveau inférieur.
- Les rôles `user` et `guest` ne sont pas proposés dans les formulaires de création/modification utilisateur.

### Permissions

- Les permissions sont créées et modifiées depuis le modal de la liste permissions.
- Routes utilisées :
  - `POST /admin/permissions` (`admin.permissions.store`)
  - `PUT /admin/permissions/{permission}` (`admin.permissions.update`)
  - `DELETE /admin/permissions/{permission}` (`admin.permissions.destroy`)
  - `POST /admin/permissions/assign` (`admin.permissions.assign`)
- Le champ `slug` est optionnel dans le modal : s'il est vide, il est généré depuis le nom.
- Le champ `action` est déduit du début du slug (`view_users` donne `view`).
- `is_active` est forcé à `true` à la création/modification depuis Settings.
- La recherche de la liste permissions est automatique côté navigateur, sans rechargement.

### Vérifications recommandées après changement

```bash
php -l app/Http/Controllers/Auth/AuthController.php
php -l app/Http/Controllers/Admin/SettingsController.php
php -l routes/web.php
php artisan route:list
```

Si `php artisan` échoue avec une erreur MySQL, vérifier que XAMPP/MySQL est démarré et que `.env` pointe vers la bonne base (`DB_DATABASE=siglab_manager`).

### Points d'attention

- Les permissions dynamiques sont lues au boot via `AppServiceProvider`. Après création d'une nouvelle permission, elle est visible en base immédiatement, mais une nouvelle requête peut être nécessaire pour que la Gate dynamique soit disponible.
- `AppServiceProvider` vérifie les tables avec `hasTableSafely()` afin que les commandes artisan (`route:list`, `php -l`, etc.) restent utilisables même si MySQL est arrêté.
- Le reset password nécessite une configuration mail fonctionnelle pour envoyer le lien. En local, utilisez Mailtrap/log mailer selon votre `.env`.
- Le projet n'a pas actuellement de dépôt git exploitable dans ce workspace (`.git` est vide). Documenter les changements dans ce README reste donc important pour la reprise par d'autres développeurs.

Souhaitez-vous que j'ajoute dans le README une section d'exemples d'appels AJAX pour l'assignation des permissions, ou que j'implémente directement l'API/AJAX pour sauvegarder les permissions depuis la page admin/settings ?

## 🗺️ Roadmap & Feature status (mai 2026)

- Users CRUD (Create / Read / Update / Delete)
	- Statut: In progress
	- Ce qui est fait: Formulaire modal pour création/édition centralisé dans `resources/views/admin/users/index.blade.php`. Validation côté serveur via `app/Http/Requests/UserRequest.php`. Les erreurs s'affichent via les flash messages de `resources/views/layouts/admin.blade.php`.
	- Problèmes connus: certaines validations côté rôle ont été corrigées (utilisation de l'enum `UserRole`). Si la création échoue, vérifier les logs `storage/logs/laravel.log` et s'assurer que l'utilisateur courant a la permission `create_user` ou est `superadmin`.
	- À faire: Soumission AJAX (optionnel) pour UX sans rechargement, feedback inline, et tests E2E.

- Permissions & Assignation
	- Statut: Partially done
	- UI: `resources/views/admin/settings.blade.php` contient la grille d'assignation par module et les cases à cocher. Les en-têtes des modules reprennent les couleurs du `--navbar-bg`/`--navbar-text`.
	- Back-end: Méthode d'assignation à implémenter (TODO). Prévoir route POST `/admin/permissions/assign` et un service dans `app/Services/PermissionService.php`.

- Auth & Seeds
	- Statut: Done / Verify
	- Les seeders pour permissions et superadmin ont été ajustés (utilisation de `firstOrCreate`). Pour garantir la présence des superadmins, exécuter :
		```bash
		php artisan migrate:fresh --seed
		```
	- Vérifier `.env` (MySQL), et `SESSION_DRIVER` si sessions posent problème.

- UI: Navbar / Sidebar / Themes
	- Statut: Done
	- Corrections appliquées: contraste hover, styles dropdown unifiés. Fichiers mis à jour: [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php#L1), [resources/views/layouts/navbar.blade.php](resources/views/layouts/navbar.blade.php#L1), [resources/views/layouts/sidebar.blade.php](resources/views/layouts/sidebar.blade.php#L1).

## 🔎 Où chercher les éléments clés

- Routes principales: [routes/web.php](routes/web.php#L1)
- Contrôleurs admin: [app/Http/Controllers/Admin](app/Http/Controllers/Admin)
- Services (logique métier): [app/Services](app/Services)
- Seeders / Migrations: [database/migrations](database/migrations) et [database/seeders](database/seeders)
- Vues administrateur: [resources/views/admin](resources/views/admin)
- Layouts globaux: [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php#L1)
- Permissions middleware: [app/Http/Middleware/CheckPermission.php](app/Http/Middleware/CheckPermission.php#L1)
- Enum roles/statuts: [app/Shared/Enums/UserRole.php](app/Shared/Enums/UserRole.php#L1), [app/Shared/Enums/UserStatus.php](app/Shared/Enums/UserStatus.php#L1)

## ✅ Quick troubleshooting

- Si la création d'utilisateur échoue sans message:
	1. Vérifier `storage/logs/laravel.log` pour exceptions.
	2. Vérifier que l'utilisateur courant a la permission `create_user` ou est superadmin.
	3. Valider que `.env` contient `DB_CONNECTION=mysql` et bonnes informations.

- Pour forcer rebuild DB (dev local) :
```bash
php artisan migrate:fresh --seed
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

## 📌 Règles et conventions

- Les rôles et statuts sont définis dans `app/Shared/Enums` et stockés en base comme leurs valeurs string.
- Utiliser les Form Requests (`app/Http/Requests`) pour toute validation.
- La logique métier doit résider dans `app/Services` et non directement dans les contrôleurs.

---

Si tu veux, je peux :
- ajouter la soumission AJAX pour le modal d'utilisateur (création/mise à jour) et afficher les messages en JSON sans rechargement ; ou
- générer un petit guide pas-à-pas pour qu'un autre dev configure l'environnement XAMPP/MySQL et démarre rapidement.

Fin de la mise à jour README (mai 2026)
