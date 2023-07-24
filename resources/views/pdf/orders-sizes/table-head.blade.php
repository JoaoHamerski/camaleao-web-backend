<thead>
    <tr>
        <th
            class="bg-primary text-white py-2"
            colspan="{{ count($ordersSizes["{$garmentType}_metadata"][$key]['sizes']) + 1 }}"
        >
            <span>
                @if ($indicators)
                [{{ $includedInMap[$garmentType]['abbr'] }}{{ $key + 1}}]
                @else
                [{{ $key + 1}}]
                @endif
                {{ __($garmentType) }}:
            </span>
            <span>
                {{ $ordersSizes["{$garmentType}_metadata"][$key]['name'] }}
            </span>
        </th>
    </tr>
    @if ($indicators)
    <thead>
        <tr class="bg-secondary">
            <th
                colspan="{{ count($ordersSizes["{$garmentType}_metadata"][$key]['sizes']) + 1 }}"
                class="text-secondary"
                style="font-weight: normal; font-size: .7rem"
            >
                <b>Legenda:</b> MO - Modelo | MA - Material | TG - Tipo de Gola | TM - Tipo de Manga
            </th>
        </tr>
    </thead>
    @endif

    <tr class="bg-secondary">
      <th class="text-left">Cód.</th>

      @foreach($ordersSizes["{$garmentType}_metadata"][$key]['sizes'] as $size)
          <th>{{ $size['name'] }}</th>
      @endforeach
    </tr>
</thead>
