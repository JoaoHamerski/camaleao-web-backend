@extends('layout')

@section('title', 'Despesas')

@section('content')
  <div class="mt-5 mx-auto">

    @role('gerencia')
    <div class="card mb-2">
      <div class="card-header font-weight-bold text-white bg-success position-relative">
        <a class="stretched-link collapsed" data-toggle="collapse" href="#collapse-filter-card" aria-expanded="true"></a>
        <div class="card-collapse">
          <i class="fas fa-filter fa-fw mr-1"></i>Filtros  
          <div class="collapse-icon">
            <i class="fas fa-caret-down fa-fw fa-2x"></i>
          </div> 
        </div>
      </div>

      <div id="collapse-filter-card" class="collapse">
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
    @endrole

    <div class="d-flex justify-content-between flex-column flex-md-row mb-2">
      <a href="{{ route('expenses.create') }}" class="btn btn-success">
        <i class="fas fa-plus fa-fw mr-1"></i>Cadastro de despesas
      </a>

      <a href="#expensesCreateModal" data-toggle="modal" class="btn btn-outline-success my-3 my-md-0">
        <i class="fas fa-plus fa-fw mr-1"></i>Cadastrar única despesa
      </a>

      @role('gerencia')
      <a href="#expenseTypesModal" data-toggle="modal" class="btn btn-outline-primary">
        <i class="fas fa-list fa-fw mr-1"></i>Tipos de despesas
      </a>
      @endrole
    </div>      

    <div class="d-flex justify-content-end">
      <form class="col-md-4 px-0" action="{{ route('expenses.index') }}" method="GET">
        <div class="form-group">
          <div class="input-group">
            <input type="text"
              class="form-control"
              name="descricao"
              id="descricao"
              value="{{ Request::query('descricao') }}" 
              placeholder="Buscar por descrição">

            <div class="input-group-append">
              <button class="btn btn-outline-primary" type="submit">Buscar</button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="card">
      <div class="card-header bg-primary text-white font-weight-bold position-relative">
        <a href="{{ route('expenses.index') }}" class="stretched-link"></a>
        <i class="fas fa-funnel-dollar fa-fw mr-1 "></i> Despesas
      </div>

      <div class="card-body px-0">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <th>Descrição</th>
              <th>Tipo</th>
              <th>Via</th>
              <th>Valor</th>
              <th>Data</th>
              <th>Comprovante</th>
              <th>Editar</th>
              @role('gerencia')
              <th>Excluir</th>
              @endrole
            </thead>

            <tbody>
              @forelse($expenses as $expense)
                <tr data-id="{{ $expense->id }}">
                  <td>{{ $expense->description }}</td>
                  @if($expense->type != null)
                    @if (strcasecmp($expense->type->name, 'mão de obra') == 0 && ! empty($expense->employee_name))
                    <td class="text-primary" data-toggle="tooltip" title="FUNCIONÁRIO: {{ $expense->employee_name }}">
                      {{ $expense->type->name }}
                    </td>
                    @else
                    <td>
                      {{ $expense->type->name }}
                    </td>
                    @endif
                  @else
                  <td>[não informado]</td>
                  @endif

                  <td>{{ $expense->via->name ?? '[não definido]' }}</td>
                  <td>{{ Mask::money($expense->value) }}</td>
                  <td>{{ Helper::date($expense->date, '%d/%m/%Y') }}</td>
                  <td class="text-center">
                    @if (! $expense->receipt_path)
                      <span data-toggle="tooltip" title="Sem comprovante cadastrado">
                    @endif

                    <button @if (! $expense->receipt_path) style="pointer-events: none;" disabled="disabled"  @endif data-toggle="modal" data-target="#viewReceiptModal" class="btn btn-outline-primary btn-view-receipt text-center">
                      <i class="fas fa-eye"></i>
                    </button>

                    @if (! $expense->receipt_path)
                      </span>
                    @endif
                  </td>
                  <td>
                    <button data-toggle="modal" data-target="#expensesEditModal" class="btn btn-outline-primary btn-edit">
                      <i class="fas fa-edit"></i>
                    </button>
                  </td>
                  @role('gerencia')
                  <td>
                    <button class="btn btn-outline-danger btn-delete">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </td>
                  @endrole
                </tr>

              @empty
              <tr class="mt-3">
                <td colspan="7" class="text-center pt-5">
                  <h5>
                  Nenhum cadastro de despesa feito por você ainda
                  </h5>
                  @role('atendimento')
                  <div class="text-center text-secondary">
                    Usuários com privilégio de atendimento podem ver apenas as próprias despesas cadastradas
                  </div>
                  @endrole
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="mt-2">
      {{ $expenses->links() }}
    </div>
  </div>

  @include('expenses.edit-modal')
  @include('expenses.create-modal')
  @include('expenses._expense-types-modal')
  @include('expenses._view-receipt-modal')
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