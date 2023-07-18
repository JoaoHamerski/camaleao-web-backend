@extends('pdf.layout')

@section('title', "Algo aqui")

@section('content')

@foreach ($groupedOrders as $key => $orders)
  <div
    @class([
      'page-break-inside-avoid',
      'page-break-after-always' => !$loop->last
    ])
  >
    <table class="table table-sm table-bordered">
      <thead>
        <tr>
          <th
            class="bg-primary text-white"
            colspan="{{ count($metadata[$key]['sizes']) + 1 }}"
          >{{ App\Models\Model::find($key)->name }}</th>
        </tr>
        <tr class="bg-secondary">
          <th class="text-left">Cód.</th>
          @foreach($metadata[$key]['sizes'] as $size)
              <th>{{ $size['name'] }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($orders as $order)
          <tr>
            <th class="text-left">{{ $order['id'] }}</th>
            @foreach($order['sizes'] as $size)
              <td class="text-center">
                {{ $size['quantity'] }}
              </td>
            @endforeach

            @if (count($order['sizes']) < count($metadata[$order['model_id']]['sizes']))
              @for($i = 0; $i < count($metadata[$order['model_id']]['sizes']) - count($order['sizes']); $i++)
                <td></td>
              @endfor
            @endif
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endforeach

@endsection
