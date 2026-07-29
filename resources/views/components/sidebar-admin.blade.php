@php
    $nonLues = \App\Models\Notification::whereHas('activite', function($q) {
        $q->where('user_id', auth()->id());
    })->where('envoye', true)->where('vue', false)->count();

    $roleActif = request()->query('role');
@endphp

<aside class="w-60 bg-white border-r border-gray-200 flex flex-col">
    <div class="p-5 border-b border-gray-200 flex items-center gap-3">
        <img src="/image/logo-fuac.jpg" alt="Logo" class="h-10 w-auto object-contain">
        <div>
            <p class="font-semibold text-sm text-gray-800">Startup-Valley</p>
            <p class="text-xs text-gray-400">Fondation UAC</p>
        </div>
    </div>

    <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">

        <p class="text-[10px] uppercase font-semibold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Administration</p>

        @php
            $sousMenuOuvert = in_array($roleActif, ['staff', 'coach', 'stagiaire']);
        @endphp

        <div class="flex items-center rounded-lg {{ request()->routeIs('admin.index') && !$roleActif ? 'bg-green-100' : '' }}">
            <a href="{{ route('admin.index') }}"
                class="flex-1 flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.index') && !$roleActif ? 'text-green-800 font-medium' : 'text-gray-500 hover:bg-green-50 hover:text-green-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Tous les utilisateurs
            </a>
            <button type="button" onclick="toggleSousMenuUtilisateurs()"
                class="px-2 py-2 text-gray-400 hover:text-green-700 transition">
                <svg id="chevron-utilisateurs" xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 transition-transform {{ $sousMenuOuvert ? 'rotate-180' : '' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <div id="sousmenu-utilisateurs" class="{{ $sousMenuOuvert ? '' : 'hidden' }} space-y-0.5">
            <a href="{{ route('admin.index', ['role' => 'staff']) }}"
                class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-sm {{ $roleActif === 'staff' ? 'bg-green-100 text-green-800 font-medium' : 'text-gray-500 hover:bg-green-50 hover:text-green-700' }}">
                Staff
            </a>

            <a href="{{ route('admin.index', ['role' => 'coach']) }}"
                class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-sm {{ $roleActif === 'coach' ? 'bg-green-100 text-green-800 font-medium' : 'text-gray-500 hover:bg-green-50 hover:text-green-700' }}">
                Coachs
            </a>

            <a href="{{ route('admin.index', ['role' => 'stagiaire']) }}"
                class="flex items-center gap-2.5 pl-9 pr-3 py-1.5 rounded-lg text-sm {{ $roleActif === 'stagiaire' ? 'bg-green-100 text-green-800 font-medium' : 'text-gray-500 hover:bg-green-50 hover:text-green-700' }}">
                Stagiaires
            </a>
        </div>

        <a href="{{ route('admin.planification') }}"
            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.planification') ? 'bg-green-100 text-green-800 font-medium' : 'text-gray-500 hover:bg-green-50 hover:text-green-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            Planification
        </a>

        <p class="text-[10px] uppercase font-semibold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Agenda</p>
        <a href="{{ route('responsable.index') }}"
            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            Mes activités
        </a>

        <a href="{{ route('responsable.notifications') }}"
            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            Notifications
            @if($nonLues > 0)
            <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 min-w-[18px] text-center">
                {{ $nonLues > 99 ? '99+' : $nonLues }}
            </span>
            @endif
        </a>

        <a href="{{ route('reseau.index') }}"
            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253" />
            </svg>
            Réseaux sociaux
        </a>

        <p class="text-[10px] uppercase font-semibold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Compte</p>
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-green-50 hover:text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            Mon profil
        </a>
    </nav>

    <div class="p-4 border-t border-gray-200">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-full bg-green-700 text-white text-xs font-semibold flex items-center justify-center">
                {{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-medium text-gray-800">{{ auth()->user()->nom }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->role }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-xs text-red-500 hover:text-red-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                Déconnexion
            </button>
        </form>
    </div>
</aside>

<script>
function toggleSousMenuUtilisateurs() {
    const sousMenu = document.getElementById('sousmenu-utilisateurs');
    const chevron = document.getElementById('chevron-utilisateurs');
    sousMenu.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
}
</script>