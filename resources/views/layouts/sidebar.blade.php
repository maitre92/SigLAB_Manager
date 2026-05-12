<aside class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebar" style="background-color: var(--sidebar-bg); min-height: calc(100vh - 56px);">
    <div class="position-sticky pt-3">
        <button type="button" id="sidebarCollapseBtn" class="sidebar-collapse-btn" aria-label="Réduire / Agrandir le menu">
            <i class="fas fa-chevron-left"></i>
        </button>
        <nav class="nav flex-column">
            
            <!-- Dashboard -->
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
               href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i> <span class="nav-text">Dashboard</span>
            </a>

            @php
                $user = Auth::user();
                $canViewLearners = $user && ($user->isSuperAdmin() || $user->hasPermission('view_learners'));
                $canViewCourses = $user && ($user->isSuperAdmin() || $user->hasPermission('view_courses'));
                $canViewPedagogical = $user && ($user->isSuperAdmin() || $user->hasAnyPermission([ 'view_pedagogical', 'view_attendance', 'view_evaluations', 'view_exams', 'view_grades' ]));
                $canViewAttendance = $user && ($user->isSuperAdmin() || $user->hasPermission('view_attendance'));
                $canViewEvaluations = $user && ($user->isSuperAdmin() || $user->hasPermission('view_evaluations'));
                $canViewExams = $user && ($user->isSuperAdmin() || $user->hasPermission('view_exams'));
                $canViewGrades = $user && ($user->isSuperAdmin() || $user->hasPermission('view_grades'));
                $canViewSchedules = $user && ($user->isSuperAdmin() || $user->hasPermission('view_schedules'));
                $canViewFinances = $user && ($user->isSuperAdmin() || $user->hasAnyPermission([ 'view_finances', 'view_payments', 'view_expenses', 'view_revenue' ]));
                $canViewPayments = $user && ($user->isSuperAdmin() || $user->hasPermission('view_payments'));
                $canViewExpenses = $user && ($user->isSuperAdmin() || $user->hasPermission('view_expenses'));
                $canViewRevenue = $user && ($user->isSuperAdmin() || $user->hasPermission('view_revenue'));
                $canViewCertificates = $user && ($user->isSuperAdmin() || $user->hasPermission('view_certificates'));
            @endphp

            <!-- Gestion des Apprenants -->
            @if($user && $canViewLearners)
                <a class="nav-link" href="#apprenants">
                    <i class="fas fa-graduation-cap"></i> <span class="nav-text">Apprenants</span>
                </a>
            @endif

            <!-- Gestion des Formations -->
            @if($user && $canViewCourses)
                <a class="nav-link" href="#formations">
                    <i class="fas fa-book"></i> <span class="nav-text">Formations</span>
                </a>
            @endif

            <!-- Gestion Pédagogique -->
            @if($user && $canViewPedagogical)
                <div class="nav-item dropdown-menu-like">
                    <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#pedagogique-menu">
                        <i class="fas fa-chalkboard"></i> Pédagogique
                        <i class="fas fa-chevron-down ms-auto" style="font-size: 12px;"></i>
                    </a>
                    <div class="collapse" id="pedagogique-menu">
                        @if($canViewAttendance)
                            <a class="nav-link" style="padding-left: 40px; font-size: 13px;" href="#presences">
                                <i class="fas fa-clipboard-check"></i> <span class="nav-text">Présences</span>
                            </a>
                        @endif
                        @if($canViewEvaluations)
                            <a class="nav-link" style="padding-left: 40px; font-size: 13px;" href="#evaluations">
                                <i class="fas fa-chart-line"></i> <span class="nav-text">Évaluations</span>
                            </a>
                        @endif
                        @if($canViewExams)
                            <a class="nav-link" style="padding-left: 40px; font-size: 13px;" href="#examens">
                                <i class="fas fa-pencil-alt"></i> <span class="nav-text">Examens</span>
                            </a>
                        @endif
                        @if($canViewGrades)
                            <a class="nav-link" style="padding-left: 40px; font-size: 13px;" href="#notes">
                                <i class="fas fa-star"></i> <span class="nav-text">Notes</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Emplois du Temps -->
            @if($user && $canViewSchedules)
                <a class="nav-link" href="#emplois">
                    <i class="fas fa-calendar-alt"></i> <span class="nav-text">Emplois du Temps</span>
                </a>
            @endif

            <!-- Gestion Financière -->
            @if($user && $canViewFinances)
                <div class="nav-section-title mt-4">Finances</div>
                <div class="nav-item dropdown-menu-like">
                    <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#finances-menu">
                        <i class="fas fa-money-bill-wave"></i> Gestion Financière
                        <i class="fas fa-chevron-down ms-auto" style="font-size: 12px;"></i>
                    </a>
                    <div class="collapse" id="finances-menu">
                        @if($canViewPayments)
                            <a class="nav-link" style="padding-left: 40px; font-size: 13px;" href="#paiements">
                                <i class="fas fa-credit-card"></i> <span class="nav-text">Paiements</span>
                            </a>
                        @endif
                        @if($canViewExpenses)
                            <a class="nav-link" style="padding-left: 40px; font-size: 13px;" href="#depenses">
                                <i class="fas fa-shopping-cart"></i> <span class="nav-text">Dépenses</span>
                            </a>
                        @endif
                        @if($canViewRevenue)
                            <a class="nav-link" style="padding-left: 40px; font-size: 13px;" href="#recettes">
                                <i class="fas fa-coins"></i> <span class="nav-text">Recettes</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Attestations -->
            @if($user && $canViewCertificates)
                <a class="nav-link" href="#attestations">
                    <i class="fas fa-certificate"></i> <span class="nav-text">Attestations</span>
                </a>
            @endif

            <!-- Gestion Documentaire -->
            <!-- Documents et Rapports supprimés par l'utilisateur -->

            <!-- Traçabilité supprimée -->

            <!-- Paramètres (nouveau) -->
            <div class="nav-section-title mt-4">Système</div>
            <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" 
               href="{{ route('admin.settings') }}">
                <i class="fas fa-cog"></i> <span class="nav-text">Paramètres</span>
            </a>

        </nav>
    </div>
</aside>

<style>
    .nav-item.dropdown-menu-like .collapse {
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .nav-item.dropdown-menu-like .collapse.show {
        display: block;
    }
</style>
