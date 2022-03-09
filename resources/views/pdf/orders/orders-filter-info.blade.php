@push('styles')
  <style>
    .table-filter {
      font-size: .85rem;
    }

    .table-filter-title {
      color: white;
      text-transform: uppercase;
    }
  </style>
@endpush
<div class="mb-3">
  <table class="table table-filter table-striped">
    <thead class="table-filter-title">
      <tr>
        <th colspan="2" class="bg-primary">Informações do filtro</th>
      </tr>
    </thead>
    <tbody>
      @if (Helper::filled($filters, 'order'))
      <tr>
        <td class="fw-bold w-25">ORDEM: </td>
        <td>{{ __($filters['order'])}}</td>
      </tr>
      @endif

      @if (Helper::filled($filters, 'state'))
      <tr>
        <td class="fw-bold w-25">PEDIDOS:</td>
        <td>{{ __($filters['state']) }}</td>
      </tr>
      @endif

      @if (Helper::filled($filters, 'city_id'))
      <tr>
        <td class="fw-bold w-25">CIDADE: </td>
        <td>
          {{ ($city = App\Models\City::find($filters['city_id']))->name }}
          {{ $city->state ? ' - ' . $city->state->abbreviation : '- N/A' }}
        </td>
      </tr>
      @endif

      @if (Helper::filled($filters, 'status_id'))
      <tr>
        <td class="fw-bold w-25">STATUS:</td>
        <td>
          {{ App\Models\Status::find($filters['status_id'])->text }}
        </td>
      </tr>
      @endif

      @if (Helper::filled($filters, 'closed_at'))
      <tr>
        <td class="fw-bold w-25">
          FECHADOS EM:
        </td>
        <td>
          {{ Mask::date($filters['closed_at'])}}
        </td>
      </tr>
      @endif

      @if (Helper::filled($filters, 'delivery_date'))
      <tr>
        <td class="fw-bold w-25">
            ENTREGA PARA:
        </td>
        <td>
          {{ Mask::date($filters['delivery_date']) }}
        </td>
      </tr>
      @endif
    </tbody>
  </table>
</div>
