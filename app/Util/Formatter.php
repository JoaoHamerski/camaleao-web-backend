<?php

namespace App\Util;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Http\UploadedFile;

class Formatter
{

    /**
     * Parser para formatar dados de um formulário chamando métodos
     * da classe \App\Util\Formatter.
     *
     * @param array $data Dados do formulário
     * @param array $options Array associativo com as opções.
     * Ex: [$methodToUse => [$fieldsToFormat], ...]
     * $fieldsToFormat são comparados como wildcard, ou seja,
     * dão match se qualquer parte da string é encontrada em $fieldsToFormat.
     *
     * @return array $data Dados formatados.
     */
    public static function parse(array $data, array $options)
    {
        foreach ($data as $key => $item) {
            foreach ($options as $method => $fields) {
                if (!method_exists(self::class, $method)) {
                    throw new Exception("Method $method doesn't exist.");
                    break;
                }

                if (Str::contains($key, $fields) && !empty($item)) {
                    if (gettype($data[$key]) === 'array') {
                        foreach ($data[$key] as $key2 => $item2) {
                            $data[$key][$key2] = self::$method($item2);
                        }
                    } else {
                        $data[$key] = self::$method($item);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Remove qualquer caractere que não é um digito da string
     *
     * @param string $str
     *
     * @return string or null
     */
    public static function stripNonDigits($str)
    {
        return $str !== null
            ? preg_replace('/\D/', '', $str)
            : null;
    }

    /**
     * Cria uma instancia de Illuminate\Http\UploadedFile
     * a partir de um arquivo em base64
     *
     * @param string $base64
     * @return Illuminate\Http\UploadedFile
     */
    public static function base64ToUploadedFile($base64)
    {
        if (Helper::isValidURL($base64)) {
            return Helper::getFilenameFromURL($base64);
        }

        if (!FileHelper::isBase64($base64)) {
            throw new Exception("The uploaded file isn't a valid file.");
        }

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

    public static function parseDate($str)
    {
        if (Validate::isDate($str)) {
            return Carbon::createFromFormat('d/m/Y', $str)->toDateString();
        }

        return $str;
    }
    /**
     * Transforma o valor em dinheiro BRL para o formato padrão.
     *
     * Ex.: R$ 12.345,67 => 12345.67
     *
     * @param string|double  $str
     *
     * @return string|null
     */
    public static function parseCurrencyBRL($str)
    {
        $str = str_replace('R$', '', $str);
        $str = str_replace('.', '', $str);
        $str = str_replace(',', '.', $str);

        return $str ? trim($str) : null;
    }

    /**
     * Formata um nome passado:
     * - Removendo todos espaços duplos ou mais.
     * - Capitalizando as palavras com algumas exceções (da, do, das, dos, de).
     *
     * @param string $str
     *
     * @return string
     */
    public static function name(string $str)
    {
        $str = mb_strtolower($str, mb_detect_encoding($str));
        $str = trim(preg_replace('/\s+/', ' ', $str));

        return Helper::ucsentence($str, ['da', 'do', 'das', 'dos', 'de']);
    }
}
