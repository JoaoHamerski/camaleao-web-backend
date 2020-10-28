@extends('layout')

@section('title', 'Despesas')

@section('content')
	<div class="col-md-10 mt-5 mx-auto">
		<div class="card mb-2">
			<div class="card-header font-weight-bold text-white bg-success position-relative">
				<a class="stretched-link" data-toggle="collapse" href="#collapse-filter-card" aria-expanded="true"></a>
				<i class="fas fa-filter fa-fw mr-1"></i>Filtros		
			</div>

			<div id="collapse-filter-card" class="collapse show">
				<div class="card-body">
					<form id="reportForm" method="GET" action="{{ route('expenses.report') }}" target="_blank">
						<label for="dia_inicial" class="font-weight-bold">Intervalo de datas</label>
						<div class="form-row d-flex flex-column flex-sm-row no-gutters">
							<div class="form-group col">
								<input type="text" 
									class="form-control" 
									data-toggle="datepicker"
									placeholder="dd/mm/aaaa"
									name="dia_inicial"
									id="dia_inicial"
									autocomplete="off">
								<small class="text-muted">Informe apenas a primeira data caso queira gerar o relatório de somente uma data</small>
							</div>
							<div class="form-group col">
								<input type="text" 
									class="form-control"
									data-toggle="datepicker"
									placeholder="dd/mm/aaaa"
									name="dia_final"
									autocomplete="off">
							</div>
						</div>
						<button id="btnGenerateReport" type="submit" class="btn btn-outline-primary">Gerar relatório</button>
					</form>	
				</div>
			</div>
		</div>

		<div class="d-flex justify-content-between flex-column flex-md-row mb-2">
			<a href="{{ route('expenses.create') }}" class="btn btn-success">
				<i class="fas fa-plus fa-fw mr-1"></i>Cadastro de despesas
			</a>

			<a href="#createFormModal" data-toggle="modal" class="btn btn-outline-success my-3 my-md-0">
				<i class="fas fa-plus fa-fw mr-1"></i>Cadastrar única despesa
			</a>

			<a href="#expenseTypesModal" data-toggle="modal" class="btn btn-outline-primary">
				<i class="fas fa-list fa-fw mr-1"></i>Tipos de despesas
			</a>
		</div>

		<div class="card">
			<div class="card-header bg-primary text-white font-weight-bold">
				<i class="fas fa-cash-register fa-fw mr-1 "></i> Despesas
			</div>

			<div class="card-body px-0">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<th>Descrição</th>
							<th>Tipo</th>
							<th>Valor</th>
							<th>Data</th>
							<th>Editar</th>
							<th>Excluir</th>
						</thead>

						<tbody>
							@foreach($expenses as $expense)
							<tr data-id="{{ $expense->id }}">
								<td>{{ $expense->description }}</td>
								<td>{{ $expense->expenseType->name ?? '[não definido]' }}</td>
								<td>{{ Mask::money($expense->value) }}</td>
								<td>{{ Helper::date($expense->date, '%d/%m/%Y') }}</td>
								<td>
									<button data-toggle="modal" data-target="#editFormModal" class="btn btn-outline-primary btn-edit">
										<i class="fas fa-edit"></i>
									</button>
								</td>
								<td>
									<button class="btn btn-outline-danger btn-delete">
										<i class="fas fa-trash-alt"></i>
									</button>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="mt-2">
			{{ $expenses->links() }}
		</div>
	</div>

	@include('expenses._expense_types_modal')
	@include('expenses._edit-form-modal')
	@include('expenses._create-form-modal')
@endsection

@push('css')
	<link rel="stylesheet" href="{{ mix('css/_date-picker.css') }}">
@endpush
@push('script')
	<script src="{{ mix('js/partials/expenses/index.js') }}"></script>
	<script src="{{ mix('js/_date-picker.js') }}"></script>

	<script>
		
	</script>
@endpush