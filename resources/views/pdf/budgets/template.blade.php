<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento</title>
    @include('pdf.styles')
    <style>
        * {
            font-family: DejaVu Sans;
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 0;
            padding: 0;
            font-weight: normal;
        }

        p {
            margin: 0;
            padding: 0;
        }

        .image {
            padding-top: .5rem;
        }

        .header {
            margin-left: 3.75rem;
            margin-right: 3.75rem;
            margin-top: 8rem;
            clear: both;
        }

        .title {
            font-size: 1.3rem;
            font-weight: bold;
            text-align: center;
            margin-top: 7rem;
            margin-bottom: 4rem;
        }

        .content {
            text-align: justify;
            margin-right: 3.75rem;
            margin-left: 3.75rem;
            margin-bottom: 4rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3rem;
        }

        .table tbody {
            font-size: .8rem;
        }
        .table thead {
            font-size: .9rem;
        }

        .table td,
        .table thead tr th {
            padding: .3rem;
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <div class="header">
        @if (isset($settings->logo))
        <div class="float-l w-25 image">
            <img class="img-fluid" src="{{ FileHelper::imageToBase64($settings->logo) }}" alt="">
        </div>
        @endif
        <div class="float-r">
            {!! $settings->header !!}
        </div>
    </div>

    <div class="title">
        ORÇAMENTO
    </div>

    <div class="content">
        <div>
            {!! $settings->content !!}
        </div>
    </div>

    <div>
        <table class="table">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>DESCRIÇÃO</th>
                    <th>QTD.</th>
                    <th>UNID.</th>
                    <th>VALOR UNIT.</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budget->product_items as $productItem)
                    <tr>
                        <td class="text-center">{{ $productItem->item }}</td>
                        <td>{{ $productItem->description }}</td>
                        <td class="text-center">{{ $productItem->quantity }}</td>
                        <td class="text-center">{{ strtoupper($productItem->unity) }}</td>
                        <td class="text-center">{{ $productItem->value }}</td>
                        <td class="fw-bold">{{ Mask::currencyBRL(bcmul($productItem->quantity, $productItem->value, 2)) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="fw-bold text-end" colspan="5">TOTAL</td>
                    <td class="fw-bold">{{ Mask::currencyBRL($total) }}</td>
                </tr>
                </tbody>
            </table>
    </div>

    <div class="date text-right mt-4">
        {!! $settings->date !!}
    </div>
</body>
</html>
