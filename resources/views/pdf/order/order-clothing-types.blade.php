@include('pdf.header', ['title' => $title])

<table class="table table-striped">
  <thead>
    <tr>
      <th class="bg-primary table-title" colspan="4">
        DETALHES DAS CAMISAS
      </th>
    </tr>
    <tr>
      <th class="bg-primary text-white">TIPO</th>
      <th class="bg-primary text-white">QTD.</th>
      <th class="bg-primary text-white">VALOR UNIT.</th>
      <th class="bg-primary text-white">TOTAL</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($order->clothingTypes as $type)
    <tr>
      <td>{{ $type->name }}</td>
      <td>{{ $type->quantity }}</td>
      <td>{{ Mask::currencyBRL($type->value) }}</td>
      <td class="fw-bold">{{ Mask::currencyBRL($type->total_value) }}</td>
    </tr>
    @endforeach
    <tr>
      <td class="fw-bold">TOTAL</td>
      <td class="fw-bold">{{ $order->quantity }}</td>
      <td ></td>
      <td class="fw-bold">{{ Mask::currencyBRL($order->original_price) }}</td>
    </tr>
    @if ($order->discount > 0)
    <tr>
      <td colspan="3" class="fw-bold">DESCONTO</td>
      <td class="fw-bold">{{ Mask::currencyBRL(-$order->discount)}}</td>
    </tr>
    <tr>
      <td colspan="3" class="fw-bold">VALOR FINAL</td>
      <td class="fw-bold"> {{ Mask::currencyBRL($order->price) }}</td>
    </tr>
    @endif
  </tbody>
</table>
