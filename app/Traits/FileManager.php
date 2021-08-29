<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

trait FileManager
{
    /**
     * Cria um novo nome para o arquivo a ser armazenado
     *
     * @param $file
     * @param $key
     *
     * @return string $filename
     */
    public function getFilename($file, $key = null)
    {
        $filename = '';
        $filename .= \Carbon\Carbon::now();
        $filename .= $key ? " ($key)" : '';
        $filename .= '.' . $file->extension();

        return $filename;
    }

    /**
     * Faz o upload dos arquivos passados da requisição
     * e gera um nome para o arquivo.
     * Retorna o nome dos arquivos armazenados em uma string JSON;
     *
     * @param $files
     * @param $path
     *
     * @return string $paths;
     */
    public function uploadFieldFiles($files, $path)
    {
        $paths = [];

        foreach (array_reverse($files) as $key => $file) {
            $filename = $this->storeFile($file, $path, $key);
            $paths[] = explode('/', $filename)[2];
        }

        return json_encode($paths);
    }

    public function storeFile($file, $path, $key = null)
    {
        return $file->storeAs(
            $path,
            $this->getFilename($file, $key)
        );
    }

    /**
     * Cria uma instancia de Illuminate\Http\UploadedFile
     * a partir de um arquivo em base64
     *
     * @param $base64
     * @return Illuminate\Http\UploadedFile
     */
    public function base64ToUploadedFile($base64)
    {
        @list(, $fileData) = explode(';', $base64);
        @list(, $fileData) = explode(',', $base64);

        $file = base64_decode($fileData);

        $tempFilepath = sys_get_temp_dir() . '/' . Str::uuid()->toString();

        file_put_contents($tempFilepath, $file);

        $file = new File($tempFilepath);

        return new UploadedFile(
            $file->getPathname(),
            $file->getFilename(),
            $file->getMimeType(),
            0,
            true
        );
    }

    /**
     * Faz o upload de todos os arquivos que foram enviados na requisição
     * do formulário e retorna um array com os as chaves sendo os campos
     * e o valor sendo o caminho das imagens em JSON.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return array $data
     */
    public function uploadAllFiles($request)
    {
        $data = [];

        foreach (['art_paths', 'size_paths', 'payment_voucher_paths'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $this->uploadFiles(
                    $request->{$field},
                    $this->getFilepath($field)
                );
            }
        }

        return $data;
    }

    /**
     * Mesma função que uploadAllFiles() mas para arquivos em base64
     *
     * @param Illuminate\Http\Request $request
     *
     * @return array $data
     */
    public function uploadAllBase64Files(array $data)
    {
        $jsonFiles = [];
        $fields = [
            'art_paths',
            'size_paths',
            'payment_voucher_paths'
        ];

        foreach ($fields as $field) {
            if (!empty($data[$field])) {
                $jsonFiles[$field] = $this->uploadFieldFiles(
                    $data[$field],
                    $this->getFilepath($field)
                );
            } else {
                $jsonFiles[$field] = '';
            }
        }

        return $jsonFiles;
    }

    /**
     * Retorna o caminho da pasta do tipo de imagem que foi armazenada.
     *
     * @param $field Campo a ser retornado.
     * @param $onlyPathname Pode ser informado "true" para retornar com "public"
     *
     * @return string|null
     */
    public function getFilepath($field, $removePublic = false)
    {
        if ($field == 'art_paths') {
            return $removePublic
                ? 'imagens_da_arte'
                : 'public/imagens_da_arte';
        }

        if ($field == 'size_paths') {
            return $removePublic
                ? 'imagens_do_tamanho'
                : 'public/imagens_do_tamanho';
        }

        if ($field == 'payment_voucher_paths') {
            return $removePublic
                ? 'comprovantes'
                : 'public/comprovantes';
        }

        if ($field == 'receipt_path') {
            return $removePublic
                ? 'comprovante_vias'
                : 'public/comprovante_vias';
        }

        return null;
    }

    public function getField($filepath)
    {
        if (Str::contains($filepath, 'imagens_da_arte')) {
            return 'art_paths';
        }

        if (Str::contains($filepath, 'imagens_do_tamanho')) {
            return 'size_paths';
        }

        if (Str::contains($filepath, 'comprovantes')) {
            return 'payment_voucher_paths';
        }

        return null;
    }

    /**
     * Retorna o caminho correto para deletar o arquivo especificado
     *
     * @param string $filepath
     *
     * @return string
     */
    public function getPathToDelete($filepath)
    {
        return Str::replaceFirst('/storage/', 'public/', $filepath);
    }

    /**
     * Adiciona ou acrescenta mais imagens no pedido passado
     *
     * @param array $data   Campos retornados da requisição do formulário
     * @param string $field     Campo a ser inserido/anexado novos arquivos
     * @param App\Models\Order $order   Pedido para ser atualizado
     *
     * @return array O caminho dos arquivos que foram armazenados ou os que já estavam antes
     * caso não tenha atualização no campo do arquivo
     */
    public function appendOrInsertFiles($data, $field, $order)
    {
        $data[$field] = $this->uploadFieldFiles(
            $data[$field],
            $this->getFilepath($field)
        );

        if ($order->{$field} != null) {
            $paths = [];

            foreach (json_decode($order->{$field}) as $path) {
                $paths[] = $path;
            }

            return array_merge($paths, json_decode($data[$field]));
        } else {
            return $data[$field];
        }
    }

    /**
     * Deleta todos os arquivos de um único campo
     *
     * @param App\Models\Order $order
     * @param array $fields
     *
     * @return void
     */
    public static function deleteField($order, $fields)
    {
        foreach ($fields as $field) {
            Storage::delete($order->getPaths($field, true));
        }
    }

    /**
     * Deleta os arquivos do campo especificado no pedido ou pedidos passado
     *
     * @param App\Models\Order | array $order
     * @param array $fields
     *
     * @return void
     */
    public static function deleteFiles($order, $fields)
    {
        if ($order instanceof \Illuminate\Database\Eloquent\Collection) {
            foreach ($order as $ord) {
                static::deleteField($ord, $fields);
            }

            return;
        }

        static::deleteField($order, $fields);
    }
}
