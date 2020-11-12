<div>
	@forelse($paths as $path)
		@if ($option == 'payment_voucher')
			<h3 class="font-weight-bold text-center">Comprovante {{ $loop->index + 1 }}</h3>
		@else
			<h3 class="font-weight-bold text-center">Imagem {{ $loop->index + 1 }}</h3>
		@endif
		
		@if (Helper::isImage($path))
			<img class="img-fluid mb-2" src="{{ $path }}" alt="">
		@else
			<div class="embed-responsive embed-responsive-16by9 mb-3">
				<iframe class="embed-responsive-item" src="{{ $path }}" allowfullscreen></iframe>
			</div>
		@endif
	@empty
		<h2 class="text-secondary text-center my-4">Nenhum arquivo armazenado</h2>
	@endforelse
</div>	