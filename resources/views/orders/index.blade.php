@extends('layout')

@section('title', 'Pedidos')

@section('content')
	<div class="mt-5">
		<div class="card">
			<div class="card-header bg-success font-weight-bold position-relative">
				<a class="text-white stretched-link" data-toggle="collapse" href="#collapse-card-report" aria-expanded="true" href="">
					<i class="fas fa-clipboard-list fa-fw mr-1"></i>Relatório por cidade ou status

				</a>
			</div>

			<div id="collapse-card-report" class="collapse">
				<div class="card-body">
					<form id="formGenerateReport" target="_blank" action="{{ route('orders.report') }}" method="POST">
						<div>
							<h5 class="font-weight-bold text-dark">Filtros</h5>

							<div class="form-row d-flex flex-column flex-md-row">
								<div class="form-group col">
									<label class="font-weight-bold" for="city">Cidade</label>
									<input list="cities"
										id="city" 
										name="city"
										class="form-control"
										type="text"
										placeholder="Nome da cidade..."
										autocomplete="off">
									<small class="text-secondary">Você pode combinar os campos para gerar o relatório</small>

									<datalist id="cities">
										@foreach($cities as $city)
											<option value="{{ $city }}"></option>
										@endforeach
									</datalist>
								</div>

								<div class="form-group col">
									<label class="font-weight-bold" for="status">Status</label>
									<select class="custom-select" name="status" id="status">
										<option value="">Selecione o status</option>
										@foreach ($status as $stat)
											<option value="{{ $stat->id }}">{{ $stat->text }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="font-weight-bold" for="">Pedidos: &nbsp;&nbsp;</label>
								<div class="custom-control custom-radio custom-control-inline">
									<input checked="checked" type="radio" id="customRadioOnlyOpen" name="only_open" class="custom-control-input" value="only_open">
									<label class="custom-control-label" for="customRadioOnlyOpen">Em aberto</label>
								</div>
								<div class="custom-control custom-radio custom-control-inline">
									<input type="radio" id="customRadioAll" name="only_open" class="custom-control-input" value="all">
									<label class="custom-control-label" for="customRadioAll">Todos</label>
								</div>
							</div>

							<button type="submit" class="btn btn-outline-primary">Gerar relatório</button>
						</div>
				</form>
				</div>
			</div>
		</div>

		<div class="card mt-2">
			<div class="card-header bg-success font-weight-bold position-relative">
				<a class="text-white stretched-link" data-toggle="collapse" href="#collapse-card-report-production">
					<i class="fas fa-clipboard-list fa-fw mr-1"></i>Relátório por data de produção
				</a>
			</div>

			<div id="collapse-card-report-production" class="collapse">
				<div class="card-body">
					<h5 class="font-weight-bold">Filtros</h5>
					<form id="formGenerateReportProduction" target="_blank" method="POST" action="{{ route('orders.reportProductionDate') }}">
						<div class="form-group">
							<label class="font-weight-bold" for="date">Data de produção </label>
							<input class="form-control" id="date" type="text" name="date" placeholder="dd/mm/yyyy">
						</div>
						<div class="form-group">
								<label class="font-weight-bold" for="">Pedidos: &nbsp;&nbsp;</label>
								<div class="custom-control custom-radio custom-control-inline">
									<input checked="checked" type="radio" id="customRadioOnlyOpen" name="only_open" class="custom-control-input" value="only_open">
									<label class="custom-control-label" for="customRadioOnlyOpen">Em aberto</label>
								</div>
								<div class="custom-control custom-radio custom-control-inline">
									<input type="radio" id="customRadioAll" name="only_open" class="custom-control-input" value="all">
									<label class="custom-control-label" for="customRadioAll">Todos</label>
								</div>
							</div>

						<button class="btn btn-outline-primary" type="submit">Gerar relatório</button>
					</form>
				</div>
			</div>
		</div>
		
	</div>

	<form method="GET" action="{{ route('orders.index') }}">
		<div class="d-flex justify-content-end mt-2">
			<div class="col-md-4 px-0">
				<div class="form-group">
					<div class="input-group">
						<input class="form-control" 
							name="codigo" 
							type="text" 
							placeholder="Por código..."
							@if(Request::has('codigo')) value="{{ Request::query('codigo') }}" @endif>
						<div class="input-group-append">
							<button class="btn btn-outline-primary" type="submit">Buscar</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	<div class="card">
		<div class="card-header bg-primary font-weight-bold text-white">
			<i class="fas fa-boxes fa-fw mr-1"></i>Lista de todos pedidos
		</div>

		<div class="card-body px-0">
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Cliente</th>
							<th>Código pedido</th>
							<th>Quantidade</th>
							<th>Valor total</th>
							<th>Total pago</th>
							<th>Data de produção</th>
							<th>Data de entrega</th>
						</tr>
					</thead>

					<tbody>
						@foreach($orders as $order)
						<tr onclick="window.location='{{ $order->path() }}'" class="clickable-link @if ($order->is_closed) table-secondary @endif">
							<td>{{ $order->client->name }}</td>
							<td>{{ $order->code }}</td>
							<td>{{ $order->quantity }}</td>
							<td>{{ Mask::money($order->price) }}</td>
							<td>{{ Mask::money($order->getTotalPayments()) }}</td>
							<td>
								{{
								 	$order->production_date 
								 		? Helper::date($order->production_date, '%d/%m/%Y')
								 		: '[não informado]' 
								 }}
							</td>
							<td>
								{{ 
									$order->delivery_date 
										? Helper::date($order->delivery_date, '%d/%m/%Y')
										: '[não informado]' 
									}}
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection

@push ('script')
	<script>
		applyCleave($('[name=date]'), cleaveDate);	

		$('#formGenerateReport button[type=submit]').on('click', function(e) {
			e.preventDefault();

			axios.post(window.location.href + '/relatorio', {
				city: $('[name=city]').val(),
				status: $('[name=status]').val(),
				only_open: $('[name=only_open]:checked').val()
			})
			.then(response => {
				$('#formGenerateReport').submit();
			})
			.catch(error => {
				console.log(error.response);
				dispatchErrorMessages(error.response.data.errors);
			});
		});

		$('#formGenerateReportProduction button[type=submit]').on('click', function(e) {
			e.preventDefault();

			axios.post(window.location.href + '/relatorio-data-producao', {
				date: $('[name=date]').val()
			})
			.then(response => {
				$('#formGenerateReportProduction').submit();
			})
			.catch(error => {
				console.log(error.response);
				dispatchErrorMessages(error.response.data.errors);
			});
		});
	</script>
@endpush