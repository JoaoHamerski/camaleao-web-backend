<?php

namespace App\Util;

use GraphQL\Experimental\Executor\Collector;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class Helper
{
    /**
     * Converte a primeira letra de cada palavra de uma sentença
     * para maíuscula usando ucfirst(), podendo ser informado um
     * array com exceções (palavras que não devem ser transformadas para ucfirst())
     *
     * @param string $sentence
     * @param string|array $except
     * @return string;
     */
    public static function ucsentence(String $sentence, $except = '')
    {
        $sentence = explode(' ', $sentence);

        $sentence = array_map(function ($value) use ($except) {
            return Str::is($except, $value)
                ? $value
                : Str::ucfirst($value);
        }, $sentence);

        return implode(' ', $sentence);
    }

    /**
     * Formata a data usando strftime.
     * Referências para $format: https://www.php.net/manual/en/function.strftime.php.
     *
     * @param string $format
     * @param string \Carbon\Carbon  $datetime
     * @return string
     */
    public static function date($datetime, $format)
    {
        if (!$datetime) {
            return '';
        }

        $datetime = self::dateToTimestamp($datetime);

        return strftime(self::replaceDateExceptions($format, $datetime), $datetime);
    }

    /**
     * Converte o valor informado para uma timestamp de valor inteiro.
     *
     * @param  string | \Carbon\Carbon $timestamp
     * @return int $timestamp
     */
    public static function dateToTimestamp($datetime)
    {
        if (is_string($datetime)) {
            return strtotime($datetime);
        }

        if (is_object($datetime) && $datetime instanceof \Carbon\Carbon) {
            return $datetime->getTimestamp();
        }

        return $datetime;
    }

    /**
     * Substitui quando o formato retorna alguma string com caractere acentuado
     * ao utilizar strftime(), isso foi feito pois o Laravel lança um exception quando
     * a função strftime() retorna uma string com caracteres de acento ou cedilha.
     *
     * @param  int $timestamp
     * @return int $timestamp
     */
    private static function replaceDateExceptions($format, $timestamp)
    {
        if (Str::contains($format, "%B") && date('n', $timestamp) == 3) {
            $format = str_replace("%B", "março", $format);
        }

        if (Str::contains($format, "%A") && date('N', $timestamp) == 6) {
            $format = str_replace("%A", "sábado", $format);
        }

        if (Str::contains($format, "%A") && date('N', $timestamp) == 2) {
            $format = str_replace("%A", "terça-feira", $format);
        }

        return $format;
    }

    /**
     * Verifica se a URL passada é uma imagem
     *
     * @param string $str
     *
     * @return string
     **/
    public static function isImage($str)
    {
        $extensions = [
            '.jpg', '.jpeg', '.jpe',
            '.jif', '.jfif', '.jfi',
            '.png', '.gif', '.web',
            '.tiff', 'tif', '.bmp',
            '.svg'
        ];

        return Str::endsWith($str, $extensions);
    }

    /**
     * Retorna a extensão da URL passada.
     *
     * @param string $str
     *
     * @return string
     **/
    public static function getExtension($str)
    {
        return pathinfo($str, PATHINFO_EXTENSION);
    }

    public static function getPublicPathFromUrl(string $url)
    {
        $strPosStorage = strpos($url, '/storage');
        $storagePath = substr($url, $strPosStorage);

        return public_path($storagePath);
    }

    /**
     * Substitui uma chave de um array por outra.
     *
     * @param array $array
     * @param string $oldKey
     * @param string $newKey
     *
     * @return array
     */
    public static function replaceKey($array, $oldKey, $newKey)
    {

        if (!array_key_exists($oldKey, $array)) {
            return $array;
        }

        $keys = array_keys($array);
        $keys[array_search($oldKey, $keys)] = $newKey;

        return array_combine($keys, $array);
    }

    /**
     * Renderiza os atributos HTML passados em forma de array associativo
     * como atributos de tag HTML.
     *
     * @param array $attributes
     *
     * @return string
     */
    public static function renderAttributes($attributes)
    {
        $attr = '';

        foreach ($attributes as $key => $value) {
            $attr .= "$key=\"$value\"";

            if (array_key_last($attributes) != $key) {
                $attr .= ' ';
            }
        }

        return $attr;
    }

    private static function getRoutePrefixName($route)
    {
        $routeName = $route['name'] ?? $route;

        return "$routeName.";
    }

    private static function getRouteFilename($route)
    {
        $filename = $route['filename'] ?? $route;

        return "$filename.php";
    }

    private static function getRouteFilepath($route, $routesPath)
    {
        $basePath = base_path();

        return $basePath . $routesPath . self::getRouteFilename($route);
    }

    public static function getLastArrayEl(array $arr)
    {
        return array_values(array_slice($arr, -1))[0];
    }

    public static function isValidUrl(string $url)
    {
        $exceptions = [':', '/', '.'];

        $url = array_map(function ($char) use ($exceptions) {
            if (Str::contains($char, $exceptions)) {
                return $char;
            }

            return rawurlencode($char);
        }, str_split($url));

        $url = join('', $url);

        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function getFilenameFromURL(string $url)
    {
        return self::getLastArrayEl(explode('/', $url));
    }

    /**
     * Verifica se todos os itens do array
     * pertence ou não à instância.
     *
     * @param array $array
     * @param mix $instance
     * @param boolean $checkForTrue
     *
     * @return array $array
     */
    public static function filterInstanceOf($array, $instance, bool $checkForTrue = true)
    {
        if (!is_array($array)) {
            return $array;
        }

        return array_filter(
            $array,
            function ($item) use ($instance, $checkForTrue) {
                if ($checkForTrue) {
                    return $item instanceof $instance;
                }

                return !($item instanceof $instance);
            }
        );
    }

    public static function lastElement(array $array)
    {
        return array_values(array_slice($array, -1))[0];
    }

    /**
     * Retorna true para: "1", "true", "on", e "yes",
     * e false para: "0", "false", "off", "no" e ""
     *
     * @param  $value
     * @return boolean
     */
    public static function parseBool($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function isValidJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public static function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

        $result = round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];

        return str_replace('.', ',', $result);
    }

    public static function formatKBytes($size, $precision = 2)
    {
        $size *= 1024;

        return self::formatBytes($size, $precision);
    }

    public static function arrayMapRecursive($callback, $array)
    {
        $func = function ($item) use (&$func, &$callback) {
            return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
        };

        return array_map($func, $array);
    }

    public static function arrayFullDiff($array1, $array2)
    {
        if ($array1 instanceof Collection) {
            $array1 = $array1->toArray();
        }

        if ($array2 instanceof Collection) {
            $array2 = $array2->toArray();
        }

        return array_merge(
            array_diff($array1, $array2),
            array_diff($array2, $array1)
        );
    }

    public static function filled($data, $field)
    {
        return isset($data[$field]) && !empty($data[$field]);
    }

    public static function filledAll($data, $fields)
    {
        foreach ($fields as $field) {
            if (!static::filled($data, $field)) {
                return false;
            }
        }

        return true;
    }

    public static function filledAny($data, $fields)
    {
        foreach ($fields as $field) {
            if (static::filled($data, $field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Transforma o valor em plural para humanos.
     *
     * @param int $value Valor
     * @param string $gender O gênero da palavra
     * @param string $word A palavra
     * @param string $wordPlural Caso o plural da palavra não termine em "s", informe uma exceção a ser usada
     */
    public static function plural($value, $gender = 'M',  $word, $wordPlural = null)
    {
        $pronoun = strtolower($gender) === 'm'
            ? 'nenhum'
            : 'nenhuma';

        if ($value === null || $value === 0) {
            return "$pronoun $word";
        }

        return $value !== 1
            ? $value . ' ' . ($wordPlural ?? $word . 's')
            : "$value $word";
    }
}
