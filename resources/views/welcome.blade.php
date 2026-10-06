<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Startup-Valley') }}  Fondation UAC</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=baloo-2:600,700,800|inter:400,500,600|ibm-plex-mono:500&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --green-deep: #0B5B27;
            --green: #12793A;
            --paper: #FAFAF8;
            --red: #E33A2E;
            --gold: #F0B429;
            --ink: #1C2B22;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--paper);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .wv-page {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--paper);
        }

        /* Fond vert avec bordure ondulee */
        .wv-wave-svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        @media (max-width: 860px) {
            .wv-wave-svg { display: none; }
            .wv-page::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0; height: 190px;
                background: var(--green-deep);
                z-index: 0;
            }
        }

        /* Confettis discrets sur le blanc */
        .wv-confetti { position: absolute; inset: 0; z-index: 1; pointer-events: none; overflow: hidden; }
        .wv-confetti span {
            position: absolute;
            border-radius: 50%;
            filter: blur(1px);
            opacity: 0;
            animation: confettiIn 1.4s ease-out forwards, confettiFloat 7s ease-in-out infinite;
        }
        .wv-confetti span:nth-child(1) { top: 10%; right: 32%; width: 13px; height: 13px; background: var(--gold); animation-delay: 0.9s, 2.3s; }
        .wv-confetti span:nth-child(2) { top: 26%; right: 10%; width: 9px; height: 9px; background: var(--red); opacity: 0.6; animation-delay: 1.1s, 2.5s; }
        .wv-confetti span:nth-child(3) { top: 58%; right: 20%; width: 8px; height: 8px; background: var(--green); animation-delay: 1.3s, 2.7s; }
        .wv-confetti span:nth-child(4) { top: 70%; right: 6%; width: 11px; height: 11px; background: var(--gold); opacity: 0.6; animation-delay: 1.5s, 2.9s; }

        @keyframes confettiIn { to { opacity: 0.55; } }
        @keyframes confettiFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }

        /* En-tete */
        .wv-header {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 34px 50px 0;
            flex-wrap: wrap;
            gap: 20px;
        }

        .wv-badge {
            height: 150px;
            width: auto;
            flex-shrink: 0;
            opacity: 0;
            animation: dropIn 0.75s cubic-bezier(0.2, 0.9, 0.3, 1.2) 0.1s forwards;
        }

        .wv-logos {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-shrink: 0;
        }

        .wv-logos img {
            height: 88px;
            width: auto;
            opacity: 0;
            animation: dropIn 0.7s cubic-bezier(0.2, 0.9, 0.3, 1.2) forwards;
        }
        .wv-logos img:nth-child(2) { animation-delay: 0.18s; }

        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-16px) scale(0.9); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Contenu principal */
        .wv-main {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            align-items: center;
            padding: 50px 40px 60px;
        }

        .wv-grid {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            display: grid;
            grid-template-columns: 0.72fr 1.28fr;
            gap: 40px;
            align-items: center;
        }

        /* Colonne gauche (verte) : mini agenda */
        .wv-side {
            color: white;
            max-width: 340px;
        }

        .wv-side-label {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--gold);
            text-align: center;
            margin: 0 0 4px;
            opacity: 0;
            animation: riseIn 0.6s ease-out 0.6s forwards;
        }

        .wv-side-year {
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 500;
            font-size: 22px;
            letter-spacing: 0.12em;
            color: var(--gold);
            text-align: center;
            margin: 14px 0 0;
            opacity: 0;
            animation: riseIn 0.6s ease-out 1.2s forwards;
        }

        .wv-week {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .wv-day {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 9px;
            padding: 8px 2px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            opacity: 0;
            transform: translateY(14px);
            animation: cardIn 0.55s ease-out forwards;
        }
        .wv-day:nth-child(1) { animation-delay: 0.7s; }
        .wv-day:nth-child(2) { animation-delay: 0.77s; }
        .wv-day:nth-child(3) { animation-delay: 0.84s; }
        .wv-day:nth-child(4) { animation-delay: 0.91s; }
        .wv-day:nth-child(5) { animation-delay: 0.98s; }
        .wv-day:nth-child(6) { animation-delay: 1.05s; }
        .wv-day:nth-child(7) { animation-delay: 1.12s; }

        .wv-day-name { font-family: 'IBM Plex Mono', monospace; font-size: 8.5px; letter-spacing: 0.02em; color: rgba(255,255,255,0.7); }
        .wv-dot { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,0.55); }

        .wv-day.is-today { background: rgba(240, 180, 41, 0.22); border-color: rgba(240, 180, 41, 0.55); }
        .wv-day.is-today .wv-day-name { color: var(--gold); }
        .wv-day.is-today .wv-dot {
            background: var(--gold);
            box-shadow: 0 0 0 0 rgba(240, 180, 41, 0.6);
            animation: pulseDot 2.4s ease-out 1.4s infinite;
        }
        .wv-today-tag {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 7.5px; letter-spacing: 0.05em; text-transform: uppercase;
            color: var(--ink); background: var(--gold);
            padding: 2px 5px; border-radius: 999px;
        }

        @keyframes pulseDot {
            0% { box-shadow: 0 0 0 0 rgba(240,180,41,0.6); }
            70% { box-shadow: 0 0 0 9px rgba(240,180,41,0); }
            100% { box-shadow: 0 0 0 0 rgba(240,180,41,0); }
        }

        /* Colonne droite (blanche) : texte */
        .wv-content { color: var(--ink); }

        .wv-brand {
            font-family: 'Baloo 2', sans-serif;
            font-weight: 800;
            font-style: italic;
            font-size: clamp(1.8rem, 3.4vw, 2.7rem);
            color: var(--red);
            margin: 0 0 8px;
            opacity: 0;
            animation: riseIn 0.7s ease-out 0.2s forwards;
        }

        h1 {
            font-family: 'Baloo 2', sans-serif;
            font-weight: 700;
            font-size: clamp(1.5rem, 2.6vw, 2.1rem);
            line-height: 1.2;
            color: var(--green-deep);
            margin: 0 0 22px;
            opacity: 0;
            animation: riseIn 0.7s ease-out 0.36s forwards;
        }

        .wv-sub {
            font-size: 1.02rem;
            line-height: 1.65;
            color: #4B564D;
            max-width: 50ch;
            margin: 0 0 32px;
            opacity: 0;
            animation: riseIn 0.7s ease-out 0.52s forwards;
        }

        .wv-cta-row {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            opacity: 0;
            animation: riseIn 0.7s ease-out 0.66s forwards;
        }

        .wv-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--green);
            color: white;
            font-family: 'Baloo 2', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.01em;
            padding: 14px 30px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 10px 24px -8px rgba(18, 121, 58, 0.55);
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }
        .wv-btn:hover {
            background: var(--green-deep);
            transform: translateY(-1px);
            box-shadow: 0 12px 28px -6px rgba(18, 121, 58, 0.6);
        }
        .wv-btn svg { width: 16px; height: 16px; }

        .wv-caption {
            font-size: 0.82rem;
            color: #7A8579;
            margin: 16px 0 0;
            opacity: 0;
            animation: riseIn 0.7s ease-out 0.82s forwards;
        }

        @keyframes riseIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes cardIn { to { opacity: 1; transform: translateY(0); } }

        @media (prefers-reduced-motion: reduce) {
            .wv-brand, h1, .wv-sub, .wv-cta-row, .wv-caption, .wv-side-label, .wv-side-year, .wv-day, .wv-badge, .wv-logos img, .wv-confetti span {
                animation: none !important; opacity: 1 !important; transform: none !important;
            }
            .wv-day.is-today .wv-dot { animation: none !important; }
        }

        @media (max-width: 860px) {
            .wv-header { flex-direction: column; align-items: center; gap: 18px; padding-top: 26px; }
            .wv-badge { height: 110px; }
            .wv-logos img { height: 64px; }
            .wv-grid { grid-template-columns: 1fr; }
            .wv-side {
                order: 2;
                margin: 30px auto 0;
                max-width: 380px;
                background: var(--green-deep);
                border-radius: 18px;
                padding: 20px 14px 24px;
            }
            .wv-content { order: 1; text-align: center; }
            .wv-sub { margin-left: auto; margin-right: auto; }
            .wv-cta-row { justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="wv-page">

        <svg class="wv-wave-svg" viewBox="0 0 100 100" preserveAspectRatio="none">
            <path d="M34,0 C27,14 19,27 25,41 C31,55 43,63 35,79 C31,89 29,94 33,100 L0,100 L0,0 Z"
                  fill="#0B5B27" />
            <path d="M38,3 C31,16 23,28 29,42 C35,56 47,64 39,79 C35,89 33,93 37,99"
                  fill="none" stroke="#F0B429" stroke-width="0.5" stroke-dasharray="1.2 1.6" opacity="0.75" />
        </svg>

        <div class="wv-confetti">
            <span></span><span></span><span></span><span></span>
        </div>

        <div class="wv-header">
            <img src="/image/expe.png" alt="Startup-Valley — 10 ans" class="wv-badge">
            <div class="wv-logos">
                <img src="/image/logo-usv.jpg" alt="Startup-Valley">
                <img src="/image/logo-fuac.jpg" alt="Fondation UAC">
            </div>
        </div>

        <main class="wv-main">
            <div class="wv-grid">
                <div class="wv-side">
                    <p class="wv-side-label">{{ ucfirst(now()->isoFormat('MMMM')) }}</p>
                    <div class="wv-week">
                        @php
                            $joursApercu = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                            $indexAuj = (int) now()->dayOfWeekIso - 1;
                        @endphp
                        @foreach($joursApercu as $i => $nom)
                        <div class="wv-day {{ $i === $indexAuj ? 'is-today' : '' }}">
                            <span class="wv-day-name">{{ $nom }}</span>
                            <span class="wv-dot"></span>
                            @if($i === $indexAuj)
                            <span class="wv-today-tag">Auj.</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <p class="wv-side-year">{{ now()->format('Y') }}</p>
                </div>

                <div class="wv-content">
                    <p class="wv-brand">Startup‑Valley</p>
                    <h1>Votre espace de coordination Fondation UAC</h1>
                    <p class="wv-sub">
                        Planifiez les activités, suivez les échéances et ne manquez plus un message 
                        Gmail, WhatsApp, Messenger et Facebook réunis dans un seul espace.
                    </p>
                    <div class="wv-cta-row">
                        <button type="button" class="wv-btn" onclick="openLoginModal()">
                            Se connecter
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M13 6l6 6-6 6" />
                            </svg>
                        </button>
                    </div>
                    <p class="wv-caption">Accès réservé  les comptes sont créés par l'administration de la Fondation.</p>
                </div>
            </div>
        </main>
    </div>
    {{-- Modale de connexion --}}
    <div id="login-modal-overlay"
         class="hidden fixed inset-0 z-50 items-start justify-center bg-black/55 p-4"
         style="backdrop-filter: blur(2px); padding-top: 60px;"
         onclick="if(event.target === this) closeLoginModal()">
        <div id="login-modal-card"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 sm:p-8 relative"
             style="transform: scale(0.95) translateY(-8px); opacity: 0; transition: transform 0.2s ease, opacity 0.2s ease;">

            <button type="button" onclick="closeLoginModal()"
                    class="absolute top-2.5 right-2.5 text-gray-400 hover:text-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="mb-3">
                <h2 class="text-sm font-bold text-gray-800" style="font-family: 'Baloo 2', sans-serif;">Bon retour</h2>
                <p class="text-gray-500 text-xs">Connectez-vous à votre espace Startup Valley</p>
            </div>

            @if($errors->any())
            <div class="mb-3 bg-red-50 border border-red-200 text-red-700 text-xs px-3 py-2 rounded-lg">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @include('auth.partials.login-form')
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            if (input) input.type = input.type === 'password' ? 'text' : 'password';
        }

        function openLoginModal() {
            const overlay = document.getElementById('login-modal-overlay');
            const card = document.getElementById('login-modal-card');
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            requestAnimationFrame(() => {
                card.style.transform = 'scale(1) translateY(0)';
                card.style.opacity = '1';
            });
            document.body.style.overflow = 'hidden';
        }

        function closeLoginModal() {
            const overlay = document.getElementById('login-modal-overlay');
            const card = document.getElementById('login-modal-card');
            card.style.transform = 'scale(0.95) translateY(-8px)';
            card.style.opacity = '0';
            setTimeout(() => {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }, 180);
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeLoginModal();
        });

        @if($errors->any())
        document.addEventListener('DOMContentLoaded', openLoginModal);
        @endif
    </script>

</body>
</html>