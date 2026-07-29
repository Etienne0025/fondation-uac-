@if(auth()->user()->role === 'administrateur')
    <x-sidebar-admin />
@else

@php
    $nonLues = \App\Models\Notification::whereHas('activite', function($q) {
        $q->where('user_id', auth()->id());
    })->where('envoye', true)->where('vue', false)->count();
@endphp

<aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between">
     <div class="p-5 border-b border-gray-200 flex items-center gap-3">
        <img src="/image/logo-fuac.jpg" alt="Logo" class="h-10 w-auto object-contain">
        <div>
            <p class="font-semibold text-sm text-gray-800">Startup-Valley</p>
            <p class="text-xs text-gray-400">Fondation UAC</p>
        </div>
    </div>
 <nav class="mt-4 flex-1 overflow-y-auto">

<p class="text-[10px] uppercase font-semibold text-gray-400 px-3 pt-4 pb-1 tracking-widest">Principal</p>

<a href="{{ route('responsable.index') }}"
    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
    {{ request()->routeIs('responsable.index') || request()->routeIs('responsable.create') || request()->routeIs('responsable.edit') ? 'bg-green-100 text-green-800 font-medium' : 'text-gray-500 hover:bg-green-50 hover:text-green-700' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
    </svg>
    Mes activités
</a>

<a href="{{ route('responsable.notifications') }}"
    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
    {{ request()->routeIs('responsable.notifications') ? 'bg-green-100 text-green-800 font-medium' : 'text-gray-500 hover:bg-green-50 hover:text-green-700' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
    </svg>
    Notifications
    <span id="sidebar-notif-badge"
        class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 min-w-[18px] text-center {{ $nonLues > 0 ? '' : 'hidden' }}">
        {{ $nonLues > 99 ? '99+' : $nonLues }}
    </span>
</a>

<a href="{{ route('reseau.index') }}"
    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
    {{ request()->routeIs('reseau.index') ? 'bg-green-100 text-green-800 font-medium' : 'text-gray-500 hover:bg-green-50 hover:text-green-700' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253" />
    </svg>
    Réseaux sociaux
</a>

  <a href="{{ route('admin.planification') }}"
            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.planification') ? 'bg-green-100 text-green-800 font-medium' : 'text-gray-500 hover:bg-green-50 hover:text-green-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            Planification
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
@endif