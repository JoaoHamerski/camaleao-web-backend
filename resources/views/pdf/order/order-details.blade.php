<table class="table">
  <thead>
    <tr>
      <th class="table-title bg-primary" colspan="4">Detalhes do pedido</th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td class="fw-bold w-25 bg-secondary">REGISTRADO EM</td>
      <td colspan="3" class="bg-secondary-light">{{ Mask::date($order->created_at, 'd/m/Y \á\s H:i') }}</td>
    </tr>
    <tr>
      <td class="fw-bold w-25 bg-secondary"">NOME</td>
      <td colspan="3" class="bg-secondary-light"> {{ $order->name ?? 'N/A' }}</td>
    </tr>

    <tr>
      <td class="fw-bold w-25 bg-secondary">CÓDIGO</td>
      <td colspan="3" class="bg-secondary-light">{{ $order->code ?? 'N/A' }}</td>
    </tr>

    <tr class="fw-bold">
      <td class="fw-bold w-25 bg-secondary">CLIENTE</td>
      <td colspan="3" class="bg-secondary-light">{{ $order->client->name }}</td>
    </tr>

    <tr>
      <td class="fw-bold w-25 bg-secondary">CIDADE</td>
      <td colspan="3" class="bg-secondary-light">
        @if ($order->client->city && $order->client->city->state)
        {{ $order->client->city->name }} - {{ $order->client->city->state->abbreviation }}
        @elseif ($order->client->city)
        {{ $order->client->city->name }} - N/A
        @else
        N/A
        @endif
      </td>
    </tr>

    <tr>
      <td class="fw-bold w-25 bg-secondary">FRETE</td>
      <td colspan="3" class="bg-secondary-light">
      @if ($order->client->shippingCompany)
      {{ $order->client->shippingCompany->name }}
      @else
      N/A
      @endif
      </td>
    </tr>

    <tr>
        <td class="fw-bold bg-secondary">ESTAMPA</td>
        <td class="bg-secondary-light">
            {{ $order->print_date ? Mask::date($order->print_date) : 'N/A' }}
        </td>

        <td class="fw-bold bg-secondary">COSTURA</td>
        <td class="bg-secondary-light">
            {{ $order->seam_date ? Mask::date($order->seam_date) : 'N/A' }}
        </td>
    </tr>

    <tr>
        <td class="fw-bold bg-secondary">ENTREGA</td>
        <td class="bg-secondary-light" colspan="3">
            {{ $order->delivery_date ? Mask::date($order->delivery_date) : 'N/A' }}
        </td>
    </tr>

    <tr>
      <td class="fw-bold w-25 bg-secondary">QUANTIDADE</td>
      <td colspan="3" class="bg-secondary-light">{{ $order->quantity }}</td>
    </tr>


    <tr classs="fw-bold">
      <td class="fw-bold w-25 bg-secondary">VALOR TOTAL</td>
      <td class="fw-bold bg-secondary-light" colspan="{{ $order->discount > 0 ? 1 : 3 }}">
        @if ($order->discount)
        {{ Mask::currencyBRL($order->original_price) }}
        @else
        {{ Mask::currencyBRL($order->price) }}
        @endif
      </td>
      @if ($order->discount > 0)
      <td class="fw-bold w-25 bg-secondary">DESCONTO</td>
      <td class="fw-bold bg-secondary-light">
        {{ Mask::currencyBRL($order->discount) }}
      </td>
      @endif
    </tr>

    @if ($order->discount > 0)
    <tr>
      <td class="fw-bold w-25 bg-secondary">VALOR FINAL</td>
      <td class="fw-bold bg-secondary-light" colspan="3">
        {{ Mask::currencyBRL($order->price) }}
      </td>
    </tr>
    @endif

    @if (! $order->isPaid())
    <tr class="text-success fw-bold">
      <td class="fw-bold w-25 bg-secondary">TOTAL PAGO</td>
      <td colspan="3" class="bg-secondary-light">{{ Mask::currencyBRL($order->total_paid) }}</td>
    </tr>

    <tr class="text-danger fw-bold">
      <td class="fw-bold w-25 bg-secondary">FALTA PAGAR</td>
      <td colspan="3" class="bg-secondary-light">{{ Mask::currencyBRL($order->total_owing) }}</td>
    </tr>
    @else
    <tr class="text-center ">
      <td colspan="4" class="bg-success text-white fw-bold">PEDIDO PAGO</td>
    </tr>
    @endif
    @if ($order->closed_at)
    <tr class="text-center">
      <td colspan="4" CLASS="bg-secondary-dark fw-bold">PEDIDO FECHADO</td>
    </tr>
    @endif

  </tbody>
</table>

@if ($order->art_paths)
<div class="text-center mx-auto mt-3">
  <img class="img-thumbnail img-fluid" src="{{
    FileHelper::imageToBase64(
      Helper::getPublicPathFromUrl($order->art_paths[0])
      )
    }}"
  >
</div>
@else
<div class="w-100 text-center mx-auto mt-3" style="position: relative">
  <div class="img-thumbnail fw-bold text-secondary">
    <div class="my-5">[SEM IMAGEM]</d>
  </div>
</div>
@endif
