<div class="card">
  <div class="card-header bg-success text-white font-weight-bold position-relative">
    <a class="stretched-link collapsed" data-toggle="collapse" href="#collapse-card-report" aria-expanded="true">
    </a>

    <div class="card-collapse">
      <i class="fas fa-clipboard-list fa-fw mr-1"></i>Relatório geral
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
          <small class="text-secondary">Você pode filtrar por apenas um ou vários campos combinados</small>
          <div class="form-row d-flex flex-column flex-md-row">
            @input([
            'id' => 'city',
            'name' => 'cidade',
            'label' => 'Cidade',
            'labelClass' => 'font-weight-bold',
            'placeholder' => 'Nome da cidade...',
            'formGroupClass' => 'col',
            'attributes' => [
            'list' => 'cities',
            'autocomplete' => 'off'
            ]
            ])

            @dataList([
            'id' => 'cities',
            'items' => $cities->pluck('name')
            ])

            @select([
            'id' => 'status',
            'name' => 'status',
            'label' => 'Status',
            'labelClass' => 'font-weight-bold',
            'formGroupClass' => 'col',
            'defaultOptionText' => 'Selecione o status',
            'items' => $status,
            'itemAttribute' => 'text',
            'itemKeyToMatch' => null
            ])

            @input([
            'id' => 'data_de_fechamento',
            'name' => 'data_de_fechamento',
            'placeholder' => 'dd/mm/aaaa',
            'formGroupClass' => 'col',
            'label' => 'Data de fechamento',
            'labelClass' => 'font-weight-bold',
            'attributes' => [
            'autocomplete' => 'off',
            'data-toggle' => 'datepicker'
            ]
            ])

            @input([
            'id' => 'data_de_entrega',
            'name' => 'data_de_entrega',
            'placeholder' => 'dd/mm/aaaa',
            'formGroupClass' => 'col',
            'label' => 'Data de entrega',
            'labelClass' => 'font-weight-bold',
            'attributes' => [
            'autocomplete' => 'off',
            'data-toggle' => 'datepicker'
            ]
            ])
          </div>


          @radio([
          'label' => 'Pedidos: ',
          'labelClass' => 'font-weight-bold',
          'name' => 'em_aberto',
          'formGroupClass' => 'mb-0',
          'inputs' => [
          [
          'id' => 'customRadioOnlyOpen',
          'value' => 'em_aberto',
          'label' => 'Em aberto',
          'checked' => true
          ],
          [
          'id' => 'customRadioAll',
          'value' => 'todos',
          'label' => 'Todos'
          ]
          ]
          ])

          @radio([
          'label' => 'Ordem: ',
          'labelClass' => 'font-weight-bold',
          'name' => 'ordem',
          'inputs' => [
          [
          'id' => 'customRadioOrder',
          'label' => 'Mais antigo',
          'value' => 'mais_antigo',
          'checked' => true
          ],
          [
          'id' => 'customRadioNewer',
          'label' => 'Mais recente',
          'value' => 'mais_recente'
          ],
          [
          'id' => 'customRadioDeliveryDate',
          'label' => 'Data de entrega',
          'value' => 'data_de_entrega'
          ]
          ]
          ])

          <button type="submit" class="btn btn-outline-primary">Gerar relatório</button>
        </div>
      </form>
    </div>
  </div>
</div>