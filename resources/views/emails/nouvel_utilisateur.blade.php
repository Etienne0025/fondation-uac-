<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; padding: 30px; }
        .card { background: white; border-radius: 12px; padding: 30px; max-width: 500px; margin: auto; }
        .logo { text-align: center; margin-bottom: 20px; font-size: 20px; font-weight: bold; color: #15803d; }
        .info { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; margin: 20px 0; }
        .label { font-size: 12px; color: #6b7280; margin-bottom: 4px; }
        .value { font-size: 15px; font-weight: bold; color: #111827; }
        .footer { text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px; }
        .btn { display: block; text-align: center; background: #15803d; color: white; padding: 12px; border-radius: 8px; text-decoration: none; margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"> <img src="/image/logo-fuac.jpg" alt=" " style="max-width: 100px; height: auto;"> FUAC Agenda </div>

        <p>Bonjour <strong>{{ $nomComplet }}</strong>,</p>
        <p>Votre compte a été créé sur la plateforme de gestion d'agenda de la <strong>Fondation UAC</strong>. Voici vos identifiants de connexion :</p>

        <div class="info">
            <div class="label">Adresse email</div>
            <div class="value">{{ $email}}</div>
            <br>
            <div class="label">Mot de passe temporaire</div>
            <div class="value">{{ $motDePasse }}</div>
        </div>

        <p style="color:#ef4444; font-size:13px;">⚠️ Pour des raisons de sécurité, veuillez changer votre mot de passe dès votre première connexion.</p>

        <a href="{{ url('/login') }}" class="btn">Se connecter à la plateforme</a>

        <div class="footer">Cet email a été envoyé automatiquement. Ne pas y répondre.</div>
    </div>
</body>
</html>