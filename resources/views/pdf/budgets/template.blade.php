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
            /* margin-top: 8rem; */
            clear: both;
        }

        .title {
            font-size: 1.3rem;
            font-weight: bold;
            text-align: center;
            margin-top: 6rem;
            margin-bottom: 2rem;
        }

        .content {
            text-align: justify;
            margin-right: 3.75rem;
            margin-left: 3.75rem;
            margin-bottom: 4rem;
        }

        .table-wrapper {
            margin-bottom: 130px;
        }

        .text-sm {
            font-size: .7.85rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
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

        .date {
            position: absolute;
            right: 0;
            bottom: 8rem;
            height: 30px;
        }

        .signature {
            position: absolute;
            bottom: 0rem;
            height: 100px;;
        }

        .signature .signature-image {
            margin-bottom: -1.7rem;
            width: 30%;
        }

        .signature .signature-name {
            font-style: italic;
        }

        .signature .signature-rubric {
            width: 200px;
            border-width: 1px;
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

    <div class="table-wrapper">
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
                        <td class="text-center">{{ Mask::currencyBRL($productItem->value) }}</td>
                        <td class="fw-bold">{{ Mask::currencyBRL(bcmul($productItem->quantity, $productItem->value, 2)) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="fw-bold text-end" colspan="5">TOTAL</td>
                    <td class="fw-bold">{{ Mask::currencyBRL($total) }}</td>
                </tr>
                </tbody>
            </table>
            <div class="text-sm">*ESTE ORÇAMENTO É VÁLIDO POR 10 DIAS.</div>
    </div>

    <div class="date text-right">
        {!! $settings->date !!}
    </div>

    @if ($settings->signature_image)
    <div class="signature text-center">
        <div class="signature-image mx-auto">
            <img class="img-fluid" src="{{ FileHelper::imageToBase64($settings->signature_image) }}" alt="">
        </div>
        <hr class="signature-rubric">
    </div>
    @endif
</body>
</html>
