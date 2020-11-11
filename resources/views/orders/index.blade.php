@extends('layout')

@section('title', 'Pedidos')

@section('content')
  <div class="mt-5">
    <div class="card">
      <div class="card-header bg-success text-white font-weight-bold position-relative">
        <a class="stretched-link collapsed" data-toggle="collapse" href="#collapse-card-report" aria-expanded="true">
        </a>
        <div class="card-collapse">
          <i class="fas fa-clipboard-list fa-fw mr-1"></i>Relatório por cidade ou status
          <div class="collapse-icon">
                <i class="fas fa-caret-down fa-fw fa-2x"></i>
            </div>
          </div>
      </div>

      <div id="collapse-card-report" class="collapse">
        <div class="card-body">
          <form id="formGenerateReport" target="_blank" action="{{ route('orders.report') }}" method="GET">
            <div>
              <h5 class="font-weight-bold text-dark">Filtros</h5>

              <div class="form-row d-flex flex-column flex-md-row">
                <div class="form-group col">
                  <label class="font-weight-bold" for="city">Cidade</label>
                  <input list="cities"
                    id="city" 
                    name="cidade"
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

              <div class="form-group mb-0">
                <label class="font-weight-bold" for="">Pedidos: &nbsp;&nbsp;</label>
                <div class="custom-control custom-radio custom-control-inline">
                  <input checked="checked" type="radio" id="customRadioOnlyOpen" name="em_aberto" class="custom-control-input" value="em_aberto">
                  <label class="custom-control-label" for="customRadioOnlyOpen">Em aberto</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="customRadioAll" name="em_aberto" class="custom-control-input" value="todos">
                  <label class="custom-control-label" for="customRadioAll">Todos</label>
                </div>
              </div>

              <div class="form-group">
                <label class="font-weight-bold" for="">Ordem: &nbsp;&nbsp;</label>
                <div class="custom-control custom-radio custom-control-inline">
                  <input checked="checked" type="radio" id="customRadioOrder" name="ordem" class="custom-control-input" value="mais_antigo">
                  <label class="custom-control-label" for="customRadioOrder">Mais antigo</label>
                </div>

                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="customRadioNewer" name="ordem" class="custom-control-input" value="mais_recente">
                  <label class="custom-control-label" for="customRadioNewer">Mais recente</label>
                </div>

                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="customRadioDeliveryDate" name="ordem" class="custom-control-input" value="data_de_entrega">
                  <label class="custom-control-label" for="customRadioDeliveryDate">Data de entrega</label>
                </div>

              </div>
              <button type="submit" class="btn btn-outline-primary">Gerar relatório</button>
            </div>
        </form>
        </div>
      </div>
    </div>

    <div class="card mt-2">
      <div class="card-header text-white bg-success font-weight-bold position-relative">
        <a class="stretched-link collapsed" data-toggle="collapse" href="#collapse-card-report-production">
        </a>

        <div class="card-collapse">
          <i class="fas fa-clipboard-list fa-fw mr-1"></i>Relátório por data de produção
          <div class="collapse-icon">
                  <i class="fas fa-caret-down fa-fw fa-2x"></i>
              </div>
        </div>
      </div>

      <div id="collapse-card-report-production" class="collapse">
        <div class="card-body">
          <h5 class="font-weight-bold">Filtros</h5>
          <form id="formGenerateReportProduction" target="_blank" method="GET" action="{{ route('orders.reportProductionDate') }}">

            <div class="form-group">
              <label class="font-weight-bold" for="data_de_producao">Data de produção </label>
              <input class="form-control" 
                id="data_de_producao" 
                type="text" 
                data-toggle="datepicker"
                autocomplete="off" 
                name="data_de_producao" 
                placeholder="dd/mm/aaaa">
            </div>

            <div class="form-group">
              <label class="font-weight-bold" for="">Pedidos: &nbsp;&nbsp;</label>
              <div class="custom-control custom-radio custom-control-inline">
                <input checked="checked" type="radio" id="customRadioOnlyOpenP" name="em_aberto" class="custom-control-input" value="em_aberto">
                <label class="custom-control-label" for="customRadioOnlyOpenP">Em aberto</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customRadioAllP" name="em_aberto" class="custom-control-input" value="todos">
                <label class="custom-control-label" for="customRadioAllP">Todos</label>
              </div>
            </div>
            <button class="btn btn-outline-primary" type="submit">Gerar relatório</button>
          </form>
        </div>
      </div>
    </div>
    
  </div>

    <div class="d-flex justify-content-between flex-column flex-xl-row mt-2">
      <div class="col-xl-6 px-0">
        <div class="form-group table-responsive">
            <div class="input-group flex-nowrap">
              <div class="input-group-prepend">
                <a href="{{ route('orders.index') }}" 
                  class="btn {{ Request::isNotFilled(['ordem', 'codigo']) ? 'btn-primary' : 'btn-outline-primary' }}">
                  Prioritários
                </a>
              </div>
              <div class="input-group-append">
                <a href="{{ route('orders.index', ['ordem' => 'mais_antigo']) }}"
                  class="btn border-left-0 {{ Request::query('ordem') == 'mais_antigo' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Mais antigos
                  </a>
                <a href="{{ route('orders.index', ['ordem' => 'mais_recente']) }}" 
                  class="btn {{ Request::query('ordem') == 'mais_recente' ? 'btn-primary' : 'btn-outline-primary' }}">
                  Mais recentes
                </a>
                <a href="{{ route('orders.index', ['ordem' => 'data_de_entrega']) }}"
                  class="btn {{ Request::query('ordem') == 'data_de_entrega' ? 'btn-primary' : 'btn-outline-primary' }}">
                  Data de entrega
                </a>
              </div>
            </div>
        </div>
      </div>

      <form class="col-md-4 px-0" method="GET" action="{{ route('orders.index') }}">
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
      </form>
    </div>

    <div>
      <small class="text-secondary">
        @if (Request::query('ordem') == 'mais_antigo')
          Exibindo pedidos por ordem de cadastro mais antigos primeiros inclusive pedidos fechados
        @elseif (Request::query('ordem') == 'mais_recente')
          Exibindo pedidos por ordem de cadastro mais recente primeiros inclusive pedidos fechados
        @elseif (Request::query('ordem') == 'data_de_entrega')
          Exibindo pedidos por ordem de data de entrega mais antiga primeiro apenas pedidos em aberto
          <br>
          (sem data de entrega informada ficam por último)
        @else
          Exibindo pedidos por ordem de cadastro mais antigo primeiros somente pedidos em aberto
        @endif
      </small>
    </div>  

  <div class="card">
    <div class="card-header bg-primary font-weight-bold text-white position-relative">
      <a href="{{ route('orders.index') }}" class="stretched-link"></a>
      <i class="fas fa-boxes fa-fw mr-1"></i>Lista de todos pedidos
    </div>

    <div class="card-body px-0">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Código do pedido</th>
              <th>Quantidade</th>
              <th>Valor total</th>
              <th>Total pago</th>
              <th>Data de produção</th>
              <th>Data de entrega</th>
            </tr>
          </thead>

          <tbody>
            @foreach($orders as $order)
            <tr data-url="{{ $order->path() }}" class="clickable-link @if ($order->isClosed()) table-secondary @endif">
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

  <div class="mt-2">
    {{ $orders->links() }}
  </div>
@endsection

@push('css')
  <link rel="stylesheet" href="{{ mix('css/_date-picker.css') }}">
@endpush
@push ('script')
  <script src="{{ mix('js/partials/orders/index.js') }}"></script>
  <script src="{{ mix('js/_date-picker.js') }}"></script>
@endpush