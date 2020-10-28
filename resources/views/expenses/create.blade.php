@extends('layout')

@section('title', 'Cadastro de despesas')

@section('content')
	<div class="col mx-auto mt-5">
		<a href="{{ route('expenses.index') }}" class="btn btn-outline-primary mb-2">
			<i class="fas fa-arrow-circle-left fa-fw mr-1"></i> Voltar
		</a>
		<div class="card">
			<div class="card-header bg-success font-weight-bold text-white">
				<i class="fas fa-cash-register fa-fw mr-1"></i> Cadastro de despesas
			</div>

			<div class="card-body">
				<h5 class="mb-3">Valor total: <strong id="totalValue">R$ 0,00</strong></h5>

				<div class="d-flex no-gutters">
					<div class="form-group col col-md-6 col-lg-4 col-xl-3">
						<label class="font-weight-bold" for="all_date">Mesma data</label> <span class="text-secondary">(opcional)</span>

						<div class="input-group">
							<input class="form-control" name="all_date" type="text" placeholder="dd/mm/aaaa">
							<div class="input-group-append">
								<button class="btn-today btn btn-outline-primary">Hoje</button>
							</div>
						</div>

						<small class="text-secondary"> Caso você queira inserir a mesma data para todas as entradas</small>
					</div>	
				</div>

				<form id="formExpenses" method="POST">

					@include('expenses._inline-form')
					
					<div class="col col-md-5 mx-auto mt-3 px-0 px-md-3">
						<button id="btnNewExpense" class="btn btn-block btn-outline-primary">
							<i class="fas fa-plus"></i>
						</button>
					</div>

					<button class="btn btn-success my-3" type="submit">
						<i class="fas fa-check fa-fw mr-1"></i> Cadastrar
					</button>
				</form>

			</div>
		</div>
	</div>
@endsection

@push('script')
	<script src="{{ mix('js/partials/expenses/create.js') }}"></script>
@endpush