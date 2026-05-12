@extends('layouts.app')

@section('title', isset($page_title) ? $page_title : 'Admin')

@section('layout_content')
<!-- Page Title -->
@if (isset($page_title))
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h1>{{ $page_title }}</h1>
        @yield('actions')
    </div>
@endif

<!-- Alerts -->
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <strong>Erreurs:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Content -->
@yield('content')
@endsection

@section('scripts')
<script>
// Global AJAX handler for user create/update forms
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form) return;
    // target only forms that post to user routes
    const action = form.getAttribute('action') || '';
    if (!action.includes('/admin/users')) return;

    // We handle via AJAX
    e.preventDefault();

    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn ? submitBtn.innerHTML : null;
    if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...'; }

    const fd = new FormData(form);
    const headers = { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}' };

    fetch(action, {
        method: form._method && form._method.value ? form._method.value : (form.method || 'POST'),
        headers: headers,
        body: fd,
        credentials: 'same-origin'
    }).then(async res => {
        const contentType = res.headers.get('content-type') || '';
        if (res.status === 422 && contentType.includes('application/json')) {
            const json = await res.json();
            showFormErrors(form, json.errors || {});
            throw new Error('validation');
        }
        if ((res.status === 200 || res.status === 201) && contentType.includes('application/json')) {
            const json = await res.json();
            // success: show flash and reload
            showGlobalAlert('success', json.message || 'Opération réussie');
            setTimeout(() => location.reload(), 800);
            return;
        }
        // If response is a redirect or HTML, just reload
        if (res.redirected || contentType.includes('text/html')) {
            location.reload();
            return;
        }
        // unknown response: try parse json
        try { 
            const j = await res.json(); 
            if (j.success === false) { 
                showFormErrors(form, {'erreur_serveur': [j.message || 'Erreur du serveur']}); 
            } 
        } catch (err) { 
            showFormErrors(form, {'erreur_inattendue': ['Une erreur inattendue s\'est produite.']}); 
        }
    }).catch(err => {
        if (err.message !== 'validation') {
            showFormErrors(form, {'erreur_connexion': ['Erreur de connexion au serveur.']});
        }
    }).finally(() => {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalText; }
    });
});

function showFormErrors(form, errors) {
    // remove previous
    const existing = form.querySelector('.ajax-errors');
    if (existing) existing.remove();

    const ul = document.createElement('div');
    ul.className = 'alert alert-danger ajax-errors';
    const list = document.createElement('ul');
    for (const key in errors) {
        (errors[key] || []).forEach(msg => {
            const li = document.createElement('li'); li.textContent = msg; list.appendChild(li);
        });
        // mark field invalid if exists
        const field = form.querySelector('[name="' + key + '"]');
        if (field) { field.classList.add('is-invalid'); }
    }
    ul.appendChild(list);
    form.prepend(ul);
}

function showGlobalAlert(type, message) {
    const container = document.createElement('div');
    container.className = `alert alert-${type} alert-dismissible fade show`;
    container.role = 'alert';
    container.innerHTML = `<i class="fas fa-check-circle"></i> ${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    const top = document.querySelector('.page-header');
    if (top && top.parentNode) top.parentNode.insertBefore(container, top.nextSibling);
    else document.body.prepend(container);
    
    // Seulement effacer automatiquement les succès ou les infos (pas les erreurs)
    if (type !== 'danger') {
        setTimeout(() => { container.classList.remove('show'); container.remove(); }, 4000);
    }
}
</script>
@yield('js')
@endsection
