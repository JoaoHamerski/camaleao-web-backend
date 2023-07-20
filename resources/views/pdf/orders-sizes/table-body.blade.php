<tbody>
    @foreach($orders as $order)
    <tr>
        <th class="text-left">
            <a
                class="decoration-none text-black"
                href="{{ $order['url'] }}"
                target="_blank"
            >{{ $order['id'] }} </a>
        </th>


        @foreach($order['sizes'] as $size)
            @if (!$isColumnEmpty($size, $ordersSizes["{$type}_metadata"][$key]))
            <td class="text-center">
                {{ $size['quantity'] === 0 ? '' : $size['quantity'] }}
            </td>
            @endif
        @endforeach
    </tr>
    @endforeach

    <tr class="text-center">
        <th>TOTAL ({{ $ordersSizes["{$type}_metadata"][$key]['total'] }})</th>
        @foreach ($ordersSizes["{$type}_metadata"][$key]['sizes'] as $size)
            <th>{{ $size['quantity'] }}</th>
        @endforeach
    </tr>
</tbody>
