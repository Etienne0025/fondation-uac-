<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 30px;">
    <div style="background: white; border-radius: 12px; padding: 30px; max-width: 500px; margin: auto;">
        <h2 style="color: #dc2626;">⚠️ Activité non réalisée</h2>
        <p>Votre activité <strong>{{ $activite->titre }}</strong> n'a pas été marquée comme faite.</p>
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin: 16px 0;">
            <p><strong>Date prévue :</strong> {{ $activite->date->isoFormat('dddd D MMMM YYYY [à] H[h]mm') }}</p>
            <p><strong>Lieu :</strong> {{ $activite->lieu }}</p>
        </div>
        <a href="{{ url('/responsable') }}" style="display:block;text-align:center;background:#dc2626;color:white;padding:12px;border-radius:8px;text-decoration:none;margin-top:20px;">
            Marquer comme fait
        </a>
    </div>
</body>
</html>