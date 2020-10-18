<?php 

namespace App\Traits;

trait FileManager 
{
	public function uploadAllFiles($request)
    {
        $data = [];

        foreach(['art_paths', 'size_paths', 'payment_voucher_paths'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $this->uploadFile(
                    $request->{$field},
                    $this->getFilepath($field)
                );
            }
        }

        return $data;
    }

    public function uploadFile($files, $path) 
    {
        $paths = [];

        foreach (array_reverse($files) as $key => $file) {
            $file->storeAs($path, $filename = $this->getFilename($file, $key));

            $paths[] = $filename;
        }

        return json_encode($paths);
    }

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

    public function getFilename($file, $key) 
    {
        $filename = '';
        $filename .= \Carbon\Carbon::now();
        $filename .= " ($key)";
        $filename .= '.' . $file->extension();

        return $filename;
    }

    public function deleteFiles($order, $fields)
    {
        foreach($fields as $field) {
            \Storage::delete($order->getPaths($field, true));
        }
    }

    public function getPathToDelete($filepath)
    {
        return \Str::replaceFirst('/storage/', 'public/', $filepath);
    }

    public function appendOrInsertFiles($data, $field, $order) 
    {
        $data[$field] = $this->uploadFile(
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
}