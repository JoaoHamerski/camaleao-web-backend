<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Util\FileHelper;
use App\Util\Formatter;
use App\Util\Helper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class OrderCreatePreRegistered
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $this->validator($data)->validate();

        $data['art_paths'] = $this->storeFiles($data['art_paths']);

        $order = Order::create(Arr::only($data, [
            'status_id',
            'art_paths',
            'print_date',
            'seam_date',
            'delivery_date',
        ]));

        if (Helper::filled($data, 'reminder')) {
            $order->notes()->create([
                'text' => $data['reminder'],
                'is_reminder' => true
            ]);
        }

        return $order;
    }

    public function getFormattedData($data)
    {
        return (new Formatter($data))
            ->date(['print_date', 'delivery_date', 'seam_date'])
            ->base64ToUploadedFile('art_paths.*')
            ->get();
    }

    public function storeFiles($files)
    {
        $paths = [];

        foreach ($files as $key => $file) {
            $paths[] = FileHelper::uploadFileToField($file, 'art_paths', $key);
        }

        return json_encode($paths);
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'art_paths' => ['array', 'min:1'],
            'art_paths.*' => ['file', 'max:1024'],
            'status_id' => ['nullable', 'exists:status,id'],
            'reminder' => ['nullable'],
            'print_date' => ['nullable', 'date'],
            'seam_date' => ['nullable', 'date'],
            'delivery_date' => ['nullable', 'date'],
        ]);
    }
}
