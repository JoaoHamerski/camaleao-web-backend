<tbody>
    @foreach($orders as $order)
    <tr>
        <td class="text-left">
            <div class="fw-bold">
                <a
                    class="decoration-none text-black"
                    href="{{ $order['url'] }}"
                    target="_blank"
                >{{ $order['id'] }} </a>
            </div>
            @if ($indicators)
            <div class="small">
                {{ $getPresentInText($order, $key) }}
            </div>
            @endif
        </td>


        @foreach($order['sizes'] as $size)
            @if (!$isColumnEmpty($size, $ordersSizes["{$garmentType}_metadata"][$key]))
            <td class="text-center">
                {{ $size['quantity'] === 0 ? '' : $size['quantity'] }}
            </td>
            @endif
        @endforeach
    </tr>
    @endforeach

    <tr class="text-center">
        <th>TOTAL ({{ $ordersSizes["{$garmentType}_metadata"][$key]['total'] }})</th>
        @foreach ($ordersSizes["{$garmentType}_metadata"][$key]['sizes'] as $size)
            <th>{{ $size['quantity'] }}</th>
        @endforeach
    </tr>
</tbody>
