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

        @input([
          'id' => 'data_de_producao',
          'name' => 'data_de_producao',
          'placeholder' => 'dd/mm/aaaa',
          'attributes' => [
            'autocomplete' => 'off',
            'data-toggle' => 'datepicker'
          ]
        ])

        @radio([
            'label' => 'Pedidos: ',
            'labelClass' => 'font-weight-bold',
            'name' => 'em_aberto',
            'formGroupClass' => 'mb-0',
            'inputs' => [
              [
                'id' => 'customRadioOnlyOpenPD',
                'value' => 'em_aberto',
                'label' => 'Em aberto',
                'checked' => true
              ],
              [
                'id' => 'customRadioAllPD',
                'value' => 'todos',
                'label' => 'Todos'
              ]
            ]
          ])
        <button class="btn btn-outline-primary" type="submit">Gerar relatório</button>
      </form>
    </div>
  </div>
</div>
