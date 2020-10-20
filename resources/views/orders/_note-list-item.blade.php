<li data-id="{{ $note->id }}" class="list-group-item d-flex flex-row justify-content-between">
	<div>{{ $note->text }}</div>
	<div class="mx-1">
		<a class="text-danger btn-delete-item" href="">Deletar</a>
	</div>
</li>