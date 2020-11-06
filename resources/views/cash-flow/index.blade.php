@extends('layout')

@section('title', 'Fluxo de caixa')

@section('content')
  
  <div class="card mt-5">
    <div class="card-header bg-success text-white font-weight-bold position-relative">
      <a class="stretched-link {{ Request::anyFilled(['dia_inicial', 'dia_final']) ? '' : 'collapsed' }}" data-toggle="collapse" href="#collapse-filter-card" aria-expanded="true"></a>
      <div class="card-collapse">
        <i class="fas fa-filter fa-fw mr-1"></i>Filtros
        <div class="collapse-icon">
          <i class="fas fa-caret-down fa-fw fa-2x"></i>
        </div>
      </div>
    </div>

    <div id="collapse-filter-card" class="collapse {{ Request::anyFilled(['dia_inicial', 'dia_final']) ? 'show' : '' }}">
      <div class="card-body">
        <form method="GET" accept="{{ route('cash-flow.index') }}">
          <label for="dia_inicial" class="font-weight-bold">Intervalo de datas</label>
          <div class="form-row d-flex flex-column flex-sm-row">
            <div class="form-group col">

              <input class="form-control" 
                type="text" 
                name="dia_inicial" 
                placeholder="dd/mm/aaaa"
                data-toggle="datepicker"
                value="{{ Request::query('dia_inicial') }}" 
                autocomplete="off">
              <small class="text-secondary">
                Informe apenas a primeira data caso queira filtrar por apenas uma data.
              </small>
            </div>

            <div class="form-group col">
              <input class="form-control" 
                type="text" 
                name="dia_final" 
                placeholder="dd/mm/aaaa"
                data-toggle="datepicker"
                value="{{ Request::query('dia_final') }}" 
                autocomplete="off">
            </div>
          </div>

          <div class="d-flex mb-3 justify-content-between justify-content-sm-start">
            <button data-target="[name=dia_inicial]" class="btn btn-outline-primary btn-today">Hoje</button>
            <button class="btn btn-outline-primary mx-3 btn-current-week">Semana atual</button>
            <button class="btn btn-outline-primary btn-current-month">Mês atual</button>
          </div>

          <div class="d-flex justify-content-between justify-content-sm-start">
            <button id="btnFilter" type="submit" class="btn btn-success mr-3">Filtrar</button>
            <a class="btn btn-outline-success" href="{{ route('cash-flow.index') }}">Zerar filtros</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="mt-3">
    <form class="d-flex justify-content-end" action="{{ route('cash-flow.index') }}" method="GET">
      <div class="col-md-4 px-0">
        <div class="form-group">
          <div class="input-group">
            <input type="text" 
              class="form-control" 
              id="descricao" 
              name="descricao" 
              value="{{ Request::query('descricao') }}" 
              placeholder="Buscar por descrição">
              <div class="input-group-append">
                <button class="btn btn-outline-primary" type="submit">Buscar</button>
              </div>
          </div>
        </div>
      </div>  
    </form>  
  </div>

  <div class="card">
    <div class="card-header bg-primary text-white font-weight-bold position-relative">
      <a href="{{ route('cash-flow.index') }}" class="stretched-link"></a>
      <i class="fas fa-cash-register fa-fw mr-1"></i> Fluxo de caixa
    </div>

    <div class="card-body px-0">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Descrição</th>
              <th>Via</th>
              <th>Valor</th>
              <th>Data</th>
              <th class="text-center">Detalhes</th>
            </tr>
          </thead>

          <tbody>
            <div class="text-center font-weight-bold">
              {{ ! Request::filled('dia_inicial') ? '' : Request::query('dia_inicial') }}
              {{ ! Request::filled('dia_final') ? '' : ' - ' . Request::query('dia_final') }}

            </div>

            @if (Request::anyFilled(['dia_inicial', 'dia_final']))
            <h5 class="mx-2 mb-3 text-center {{ $balance < 0 ? 'text-danger' : 'text-success' }}">
              <span class="font-weight-bold">BALANÇO FINAL:</span> 
              <span>{!! Mask::money($balance, true) !!}</span>
            </h5>
            @endif

            @forelse($entries as $entry)
              <tr class="@if($entry instanceof \App\Models\Payment) table-success @else table-danger @endif"
                @if ($entry instanceof \App\Models\Payment) 
                data-payment-id="{{ $entry->id }}"
                @else
                data-expense-id="{{ $entry->id }}"
                @endif
              >
                @if ($entry instanceof \App\Models\Payment)
                  <td>
                    <a target="_blank" href="{{ $entry->order->path() }}">
                      {{ $entry->order->name ?? $entry->order->code . ' [cód.]' }}
                    </a>
                  </td>
                @else
                  <td>{{ $entry->description ?? '[sem descrição]' }}</td>
                @endif

                @if ($entry->via)
                  <td>{{ $entry->via->name }}</td>
                @else
                  <td>[sem via]</td>
                @endif

                <td class="font-weight-bold">
                  @if ($entry instanceof \App\Models\Expense)
                  -
                  @endif
                  {{ Mask::money($entry->value) }}
                </td>

                <td class="font-weight-bold">{{ Helper::date($entry->date, '%d/%m/%Y') }}</td>
                <td class="text-center">
                  <button data-toggle="modal" data-target="#detailsModal" class="btn btn-sm btn-outline-primary btn-view-detail">
                      <i class="fas fa-eye"></i>
                    </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5">
                  <h5 class="text-center mt-3 text-secondary">Nenhum registro que corresponda aos filtros</h5>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @include('cash-flow.modal-details')

  <div class="mt-2">
     {{ $entries->links() }}
  </div>
@endsection

@push('css')
  <link rel="stylesheet" href="{{ mix('css/_date-picker.css') }}">
@endpush

@push('script')
  <script src="{{ mix('js/partials/cash-flow/index.js') }}"></script>
  <script src="{{ mix('js/_date-picker.js') }}"></script>
@endpush