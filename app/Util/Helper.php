<?php 

namespace App\Util;

class Helper {
	/**
	 * Converte a primeira letra de cada palavra de uma sentença 
	 * para maíuscula, podendo ser informado um array com exceções
	 * (palavras que não devem ser transformadas para ucfirst())
	 * 
	 * @param string $sentence
	 * @param string|array $except
	 * @return string;
	 */
	public static function ucsentence(String $sentence, $except = '')
	{
		$sentence = explode(' ', $sentence);

		$sentence = array_map(function($value) use ($except) {
			return \Str::is($except, $value)
				? $value
				: \Str::ucfirst($value);
		}, $sentence);

		return implode(' ', $sentence);
	}

	/**
     * Formata a data usando strftime.
     * Referências para $format: https://www.php.net/manual/en/function.strftime.php.
     *
     * $datetime aceita datas em formato de string e instância de Carbon.
     * 
     * 
     * @param string $format
     * @param mix $datetime
     * @return string
     */
	public static function date($datetime, $format)
	{
		if (! $datetime) {
			return '';
		}

		$datetime = self::dateToTimestamp($datetime);

		return strftime(self::replaceDateExceptions($format, $datetime), $datetime);
	}

	/**
     * Converte o valor informado para uma timestamp de valor inteiro.
     * 
     * 
     * @param  mix $timestamp
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
     * @param  mix $timestamp
     * @return int $timestamp
     */ 
	private static function replaceDateExceptions($format, $timestamp)
	{
		if (\Str::contains($format, "%B") && date('n', $timestamp) == 3) {
			$format = str_replace("%B", "março", $format);
		}

		if(\Str::contains($format, "%A") && date('N', $timestamp) == 6) {
			$format = str_replace("%A", "sábado", $format);
		}

		if(\Str::contains($format, "%A") && date('N', $timestamp) == 2) {
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
		$extensions = ['.jpg', '.jpeg', '.jpe', '.jif', '.jfif', '.jfi', '.png', '.gif', '.web', '.tiff', 'tif','.bmp', '.svg'];

		return \Str::endsWith($str, $extensions);
	}

	/**
	 *	Retorna a extensão da URL passada.
	 * 
	 * @param string $str
	 * 
	 * @return string 
	**/
	public static function getExtension($str) 
	{
		return pathinfo($str, PATHINFO_EXTENSION);
	}

	/**
	 *	Converte o URL da image passada para base64.
	 * 
	 * @param string $imagePath
	 * 
	 * @return string
	**/
	public static function imageTo64($imagePath) 
	{
		$type = pathinfo($imagePath, PATHINFO_EXTENSION);
		$data = file_get_contents($imagePath);

		return 'data:image/' . $type . ';base64,' . base64_encode($data);
	}

	/**
	 * Substitui uma chave de um array por outra.
	 * 
	 * @param array $array
	 * @param string $old_key
	 * @param string $new_key
	 * 
	 * @return array
	 */
	public static function replaceKey($array, $old_key, $new_key) {

	    if (! array_key_exists($old_key, $array)) {
	        return $array;
	    }

	    $keys = array_keys($array);
	    $keys[array_search($old_key, $keys)] = $new_key;

	    return array_combine($keys, $array);
	}
}