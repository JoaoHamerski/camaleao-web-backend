@extends('layout')

@section('title', 'Lixeira')

@section('content')
  <div class="mt-5">
    <div class="card">
      <div class="card-header bg-danger text-white font-weight-bold">
        <i class="fas fa-trash-alt fa-fw mr-1"></i>Lixeira
      </div>

      <div class="card-body px-0 pt-0">
          <div>
            <div class="list-group list-group-horizontal border-0 font-weight-bold" id="list-tab" role="tablist">
              <a class="list-group-item list-group-item-action active rounded-0" id="list-clients-list" data-toggle="list" href="#list-clients">
                <i class="fas fa-list fa-fw mr-1"></i>Clientes
              </a>

              <a class="list-group-item list-group-item-action" id="list-orders-list" data-toggle="list" href="#list-orders">
                <i class="fas fa-boxes fa-fw mr-1"></i>Pedidos
              </a>

              <a class="list-group-item list-group-item-action" id="list-settings-list" data-toggle="list" href="#list-settings">
                <i class="fas fa-users fa-fw mr-1"></i>Usuários
              </a>

              <a class="list-group-item list-group-item-action rounded-0" id="list-expenses-list" data-toggle="list" href="#list-expenses">
                <i class="fas fa-funnel-dollar fa-fw mr-1"></i>Despesas
              </a>
            </div>
          </div>

          <div>
            <div class="tab-content" id="nav-tabContent">
              <div class="tab-pane fade show active" id="list-clients">
                @include('trash._clients-tab')
              </div>

              <div class="tab-pane fade" id="list-orders">
                @include('trash._orders-tab')
              </div>
              <div class="tab-pane fade" id="list-expenses" >...</div>
              <div class="tab-pane fade" id="list-settings" >...</div>
            </div>
          </div>
      </div>
    </div>
  </div>
@endsection