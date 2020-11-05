
@if ($expense->getReceiptPath())
	@if (Helper::isImage($expense->getReceiptPath()))
		<div class="text-center">
			<img class="img-fluid" src="{{ $expense->getReceiptPath() }}" alt="">
		</div>
	@else
		<div class="embed-responsive embed-responsive-16by9 mb-3">
			<iframe class="embed-responsive-item" src="{{ $expense->getReceiptPath() }}" allowfullscreen></iframe>
		</div>
	@endif
@else
	<div class="text-center my-5">
		<h4>Sem comprovante cadastrado</h4>
	</div>
@endif