<h2>Validation requise</h2>
<p>Vous êtes appelé(e) à valider le Terme De Référence suivant :</p>
<p><strong>Thème :</strong> {{ $tdr->titre }}</p>
<p><strong>Projet :</strong> {{ $tdr->projet->titre }}</p>
<p><strong>Publié par :</strong> {{ $tdr->user->nom }} {{ $tdr->user->prenom }}</p>
@if($tdr->superieur)
<p><strong>Supérieur hiérarchique désigné :</strong> {{ $tdr->superieur->nom }} {{ $tdr->superieur->prenom }}</p>
@endif
<p><a href="{{ route('tdr.show', $tdr) }}">Consulter et approuver le TDR</a></p>