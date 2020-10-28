<?php 

namespace App\Traits;

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
    public function getFilename($file, $key) 
    {
        $filename = '';
        $filename .= \Carbon\Carbon::now();
        $filename .= " ($key)";
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
    public function uploadFiles($files, $path) 
    {
        $paths = [];

        foreach (array_reverse($files) as $key => $file) {
            $file->storeAs($path, $filename = $this->getFilename($file, $key));

            $paths[] = $filename;
        }

        return json_encode($paths);
    }

    /**
     * Retorna o caminho da pasta do tipo de imagem que foi armazenada.
     * 
     * @param $field Campo a ser retornado.
     * @param $onlyPathname Pode ser informado "true" para retornar com "public"
     * 
     * @return string|null
    */
    public function getFilepath($field, $onlyPathName = false)
    {
        if ($field == 'art_paths')
            return $onlyPathName 
                ? 'imagens_da_arte'
                : 'public/imagens_da_arte';

        if ($field == 'size_paths')
            return $onlyPathName
                ? 'imagens_do_tamanho'
                : 'public/imagens_do_tamanho';

        if ($field == 'payment_voucher_paths')
            return $onlyPathName
                ? 'comprovantes'
                : 'public/comprovantes';

        return null; 
    }

    public function getField($filepath)
    {
        if (\Str::contains($filepath, 'imagens_da_arte'))
            return 'art_paths';

        if (\Str::contains($filepath, 'imagens_do_tamanho'))
            return 'size_paths';

        if (\Str::contains($filepath, 'comprovantes'))
            return 'payment_voucher_paths';

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
        return \Str::replaceFirst('/storage/', 'public/', $filepath);
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

        foreach(['art_paths', 'size_paths', 'payment_voucher_paths'] as $field) {
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
        $data[$field] = $this->uploadFiles(
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
    public static function deleteField($order, $fields) {
        foreach($fields as $field) {
            \Storage::delete($order->getPaths($field, true));
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
            foreach($order as $ord) {
                static::deleteField($ord, $fields);
            }

            return;
        }
     
        static::deleteField($order, $fields);
    }
}