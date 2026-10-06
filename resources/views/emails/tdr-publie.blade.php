<h2>Nouveau TDR publié</h2>
<p><strong>{{ $tdr->user->nom }} {{ $tdr->user->prenom }}</strong> a publié un nouveau Terme De Référence :</p>
<p><strong>Thème :</strong> {{ $tdr->titre }}</p>
<p><strong>Projet :</strong> {{ $tdr->projet->titre }}</p>
@if($tdr->description)
<p>{{ $tdr->description }}</p>
@endif
<p><a href="{{ route('tdr.show', $tdr) }}">Consulter le TDR</a></p>