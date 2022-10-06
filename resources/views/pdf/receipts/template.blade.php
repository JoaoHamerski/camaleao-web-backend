<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de pagamento</title>
    @include('pdf.styles')
    <style>
        * {
            font-family: sans-serif
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 0;
            padding: 0;
            font-weight: normal;
        }

        p {
            margin: 0;
            padding: 0;
            /* line-height: .25r;em; */

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

        .value {
            margin-top: 8rem;
            margin-bottom: 4rem;
        }

        .content {
            text-align: justify;
            margin-right: 3.75rem;
            margin-left: 3.75rem;
            margin-bottom: 4rem;
        }

        .date {
            margin-bottom: 14rem;
        }

        .signature .signature-image {
            width: 30%;
        }

        .signature .signature-name {
            font-style: italic;
        }
        .signature .signature-rubric {
            width: 300px;
            border-width: 1px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if (isset($settings->logo))
        <div class="float-l w-25 image">
            <img class="img-fluid" src="{{ FileHelper::imageToBase64(storage_path($settings->logo)) }}" alt="">
        </div>
        @endif
        <div class="float-r">
            {!! $settings->header !!}
        </div>
    </div>

    <div class="value">
        <h2 class="text-center fw-bold">RECIBO (R$ {{ $data['value'] ?? '%value%' }})</h2>
    </div>

    <div class="content">
        <div>
            {!! $settings->content !!}
        </div>
    </div>

    <div class="date text-right mt-4">
        {!! $settings->date !!}
    </div>

    <div class="signature text-center">
        @if (
            (isset($data['has_signature']) && $data['has_signature']) || isset($preview) ?: false
        )
            <div class="signature-image mx-auto">
                <img class="img-fluid" src="{{ FileHelper::imageToBase64(storage_path($settings->signature_image)) }}" alt="">
            </div>
        @endif
        <hr class="signature-rubric">
        <span class="signature-name">
            {{ $settings->signature_name }}
        </span>
    </div>
</body>
</html>
