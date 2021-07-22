<x-card class="mt-2"
  header-color="success"
  :is-collapsed="true"
  collapse-id="report-production"
  icon="fas fa-clipboard-list"
>
  <x-slot name="header">
    Relatório por data de produção
  </x-slot>
  
  <x-slot name="body">
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
  </x-slot>
</x-card>
