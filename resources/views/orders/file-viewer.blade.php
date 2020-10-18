<div>
	@forelse($paths as $path)
		@if (Helper::isImage($path))
			<img class="img-fluid mb-2" src="{{ $path }}" alt="">
		@else
			<h3 class="font-weight-bold text-center">Comprovante {{ $loop->index + 1 }}</h3>
			<div class="embed-responsive embed-responsive-16by9 mb-3">
				<iframe class="embed-responsive-item" src="{{ $path }}" allowfullscreen></iframe>
			</div>
		@endif
	@empty
		<h2 class="text-secondary text-center my-4">Nenhum arquivo armazenado</h2>
	@endforelse
</div>	