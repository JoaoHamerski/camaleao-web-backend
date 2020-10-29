<?php

namespace App\Util;

class Mask {

	/**
	 * Cria uma máscara definida com "#" para a string passada
	 * 
	 * @param string $mask
	 * @param string $str
	 * @return string
	 * 
	 */
	public static function mask(string $mask, string $str)
	{
	    $str = str_replace(" ", "", $str);

	    for ($i = 0; $i < strlen($str); $i++){
	        $mask[strpos($mask, "#")] = $str[$i];
	    }

	    return $mask;
	}

	/**
	 * Cria uma máscara para o número de telefone passado
	 * 
	 * @param string $str
	 * @return string or null;
	 */
	public static function phone($str)
	{
		if (strlen($str) === 11) {
			return isset($str) 
				? self::mask('(##) # ####-####', $str)
				: null;
		}

		return isset($str) 
			? self::mask('(##) ####-####', $str)
			: null;
	}

	/**
	 * Formata um valor double para dinheiro em BRL 
	 * Ex.: "123,456.78" => "R$ 123.456,78"
	 * 
	 * @param string $str, 
	 * @param bool $strongValue Retorna o valor entre tags <strong>
	 * @return string or null;
	 */
	public static function money($str, $strongValue = false)
	{
		$formatter = new \NumberFormatter('pt_BR', \NumberFormatter::CURRENCY);
		$money = $formatter->formatCurrency($str, 'BRL');

		if ($strongValue) {
			$money = explode(',', $money);
			$money = '<strong>' . $money[0] . '</strong>,' . $money[1];
		}

		return isset($str) 
			? $money 
			: null;
	}
}