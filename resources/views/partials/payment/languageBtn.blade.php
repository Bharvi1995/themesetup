<!-- Language select -->
<div class="btn-group">
    <button type="button" class=" btn-sm btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-globe">
            <circle cx="12" cy="12" r="10" />
            <line x1="2" x2="22" y1="12" y2="12" />
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
        </svg>
        {{ ucfirst(session()->get('locale') ?? 'en') }}
    </button>
    <ul class="dropdown-menu langDropdown" style="background: #1B1919;">
        <li class="dropdown-item langListBtn" data-lang="en">En</li>
        <li class="dropdown-item langListBtn" data-lang="fr">Fr</li>
        <li class="dropdown-item langListBtn" data-lang="sp">Sp</li>

    </ul>
</div>
