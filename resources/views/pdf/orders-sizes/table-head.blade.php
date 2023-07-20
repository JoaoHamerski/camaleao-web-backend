<thead>
    <tr>
        <th
            class="bg-primary text-white py-2"
            colspan="{{ count($ordersSizes["{$type}_metadata"][$key]['sizes']) + 1 }}"
        >
            <span>{{ __($type) }}: </span>
            <span>
                {{ $ordersSizes["{$type}_metadata"][$key]['name'] }}
            </span>
        </th>
    </tr>

    <tr class="bg-secondary">
      <th class="text-left">Cód.</th>

      @foreach($ordersSizes["{$type}_metadata"][$key]['sizes'] as $size)
          <th>{{ $size['name'] }}</th>
      @endforeach
    </tr>
</thead>
