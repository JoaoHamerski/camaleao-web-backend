<?php

namespace App\Http\Controllers;

use App\Util\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ImagesController extends Controller
{
    /**
     * Mapeamento dos campos do banco de dados que armazenam arquivos
     * para o nome dos diretórios em "storage/public"
     */
    protected static $FIELDS_FOLDER_MAP = [
        'art_paths' => 'imagens_da_arte',
        'size_paths' => 'imagens_do_tamanho',
        'payment_voucher_paths' => 'comprovantes',
        'receipt_path' => 'comprovante_vias'
    ];

    public function show(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $field = $request->get('field');
        $filename = $request->get('filename');
        $storagePath = storage_path('app/public');
        $fieldFolder = self::$FIELDS_FOLDER_MAP[$field];
        $fileURL = "$storagePath/$fieldFolder/$filename";

        return response()->file($fileURL);
    }
}
