<?php

namespace App\Util;

use App\GraphQL\Exceptions\UnprocessableException;
use Exception;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Formatter
{
    protected $data;
    protected $pathDelimiter;
    protected $propertyAccessor;

    /**
     * @param array $data Dados a serem formatados.
     * @param string $pathDelimiter Delimitador quando buscar array
     * associativos pelo caminho do array.
     */
    public function __construct(array $data, string $pathDelimiter = '.')
    {
        $this->data = $data;
        $this->pathDelimiter = $pathDelimiter;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Mapeia cada item dos dados recursivamente,
     * processando-os com a função anonima passada em $callback
     *
     * @param callback $callback
     * @return \App\Util\Formatter
     */
    public function map(callable $callback = null)
    {
        if (!$callback) {
            return $this;
        }

        $this->data = Helper::arrayMapRecursive($callback, $this->data);

        return $this;
    }
    /**
     * Retorna em array os dados formatados.
     *
     * @param bool $convertEmptyStringsToNull
     * @return array
     */
    public function get($convertEmptyStringsToNull = true): array
    {
        if ($convertEmptyStringsToNull) {
            $this->map(fn ($item) => $item === '' ? null : $item);
        }

        return $this->data;
    }

    public function getData($field = null)
    {
        $field = $this->parseField($field);

        if (!$field) {
            return $this->data;
        }

        return $this->propertyAccessor->getValue(
            $this->data,
            $field
        );
    }

    public function setData($field, $value)
    {
        $field = $this->parseField($field);

        $this->propertyAccessor->setValue(
            $this->data,
            $field,
            $value
        );
    }

    /**
     * Analisa o campo passado caso tenha paths,
     * convertendendo os paths para o formato aceitado
     * por PropertyAccess class.
     *
     * @param string $path Caminho a ser analisado
     * @return null|string
     */
    private function parseField(string $path)
    {
        $path = explode($this->pathDelimiter, $path);

        if (count($path) === 1 && !$path[0]) {
            return null;
        }

        $path = array_map(fn ($item) => "[$item]", $path);

        return implode('', $path);
    }

    /**
     * Verifica se o path passado possui wildcards,
     * caso tenha constrói um array para ser analisado por
     * PropertyAccess class, senão apenas o campo
     *
     * @param string $field Campo a ser analisado
     * @return string|array<string>
     */
    private function parsePath(string $field)
    {
        $delimiter = "{$this->pathDelimiter}*{$this->pathDelimiter}";

        if (!Str::contains($field, '*')) {
            return $field;
        }

        if (Str::endsWith($field, "$this->pathDelimiter*")) {
            $delimiter = "$this->pathDelimiter*";
        }

        $splittedField = explode($delimiter, $field)[0];
        $data = $this->getData($splittedField);

        if (!$data) {
            return $field;
        }

        $fields = [];

        for ($i = 0; $i < count($data); $i++) {
            $fields[] = str_replace('*', $i, $field);
        }

        return $fields;
    }

    /**
     * Verifica se pode ser aplicada a formatação no campo especificado.
     *
     * @param string $field
     * @return bool
     */
    public function isFieldFormattable(string $field): bool
    {
        return $this->getData($field) !== null
            && $this->getData($field) !== '';
    }

    /**
     * Aplica o método de formatação no campo especificado.
     *
     * @param string $method
     * @param array|string $field
     * @return void
     */
    public function applyMethod(string $method, $fields): void
    {
        $fields = $this->parsePath($fields);

        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (!$this->isFieldFormattable($field)) {
                    continue;
                }

                $this->setData(
                    $field,
                    self::$method($this->getData($field))
                );
            }

            return;
        }

        if (!$this->isFieldFormattable($fields)) {
            return;
        }

        $this->setData(
            $fields,
            self::$method($this->getData($fields))
        );
    }

    /**
     * Aplica os métodos de "parse" nos campos especificados
     *
     * @param string|array<string> $fields
     * @return \App\Util\Formatter
     */
    public function applyParse($fields, $method): Formatter
    {
        $method = "parse" . ucfirst($method);

        if (is_array($fields)) {
            foreach ($fields as $field) {
                $this->applyMethod($method, $field);
            }

            return $this;
        }

        $this->applyMethod($method, $fields);

        return $this;
    }

    /**
     * Remove qualquer caractere que não é um digito da string
     *
     * @param string $value
     * @return string|null
     */
    public static function parseStripNonDigits($value)
    {
        return $value !== null
            ? preg_replace('/\D/', '', $value)
            : null;
    }

    /**
     * Remove qualquer caractere que não é um digito da string
     *
     * @param array<string>|string $fields Campos para aplicar a formatação
     * @return \App\Util\Formatter
     */
    public function stripNonDigits($fields): Formatter
    {
        return $this->applyParse($fields, __FUNCTION__);
    }

    /**
     * Cria uma instancia de Illuminate\Http\UploadedFile
     * a partir de um arquivo em base64
     *
     * @param string $value
     * @return \Illuminate\Http\UploadedFile
     */
    public static function parseBase64ToUploadedFile($value)
    {
        if (Helper::isValidURL($value)) {
            return Helper::getFilenameFromURL($value);
        }

        if (empty($value)) {
            return $value;
        }

        if (!FileHelper::isBase64($value)) {
            throw new UnprocessableException(
                "O arquivo enviado não é válido",
                "Apenas arquivo em base64 devem ser enviados na requisição."
            );
        }

        @list(, $fileData) = explode(';', $value);
        @list(, $fileData) = explode(',', $value);

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
     * Cria uma instancia de Illuminate\Http\UploadedFile
     * a partir de um arquivo em base64
     *
     * @param array<string>|string $fields Campos para aplicar a formatação
     * @return \App\Util\Formatter
     */
    public function base64ToUploadedFile($fields): Formatter
    {
        if (is_array($fields) && empty($fields)) {
            return $this;
        }

        return $this->applyParse($fields, __FUNCTION__);
    }

    /**
     * Formata uma data em d/m/Y para o formato ISO/SQL
     *
     * @param string $value no formato d/m/Y
     * @return string $date no formato Y-m-d
     */
    public static function parseDate(string $value)
    {
        try {
            $date = Carbon::createFromFormat('d/m/Y', $value);
            $date = $date->toDateString();
        } catch (InvalidFormatException $exception) {
            return response('A data informada é inválida.', 500);
        }

        return $date;
    }

    /**
     * Formata uma data em d/m/Y para o formato ISO/SQL
     *
     * @param array<string>|string $fields Campos para aplicar a formatação
     * @return \App\Util\Formatter
     */
    public function date($fields): Formatter
    {
        return $this->applyParse($fields, __FUNCTION__);
    }

    /**
     * Transforma o valor em dinheiro BRL para o formato padrão.
     *
     * Ex.: R$ 12.345,67 => 12345.67
     *
     * @param string|double  $str
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
     * Transforma o valor em dinheiro BRL para o formato padrão.
     *
     * Ex.: R$ 12.345,67 => 12345.67
     *
     * @param array<string>|string $fields Campos para aplicar a formatação
     * @return \App\Util\Formatter
     */
    public function currencyBRL($fields): Formatter
    {
        return $this->applyParse($fields, __FUNCTION__);
    }

    /**
     * Formata um nome passado:
     * - Removendo todos espaços duplos ou mais.
     * - Capitalizando as palavras com algumas exceções (da, do, das, dos, de).
     *
     * @param string $str
     * @return string
     */
    public static function parseName($value)
    {
        $value = mb_strtolower($value, mb_detect_encoding($value));
        $value = trim(preg_replace('/\s+/', ' ', $value));

        return Helper::ucsentence($value, ['da', 'do', 'das', 'dos', 'de']);
    }

    /**
     * Formata um nome passado:
     * - Removendo todos espaços duplos ou mais.
     * - Capitalizando as palavras com algumas exceções (da, do, das, dos, de).
     *
     * @param array<string>|string $fields Campos para aplicar a formatação
     * @return \App\Util\Formatter
     */
    public function name($fields): Formatter
    {
        return $this->applyParse($fields, __FUNCTION__);
    }

    public function parseCapitalize($value)
    {
        return Str::ucfirst($value);
    }

    public function capitalize($fields): Formatter
    {
        return $this->applyParse($fields, __FUNCTION__);
    }

    public function parseSnake($value)
    {
        return Str::snake($value);
    }

    public function snake($fields): Formatter
    {
        return $this->applyParse($fields, __FUNCTION__);
    }
}
