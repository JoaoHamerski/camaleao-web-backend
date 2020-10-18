<?php

namespace App\Util;

class Sanitizer {

	/**
	 * Remove qualquer caractere que não é um digito da string
	 * 
	 * @param string $str
	 * @return string or null
	 */
	public static function removeNonDigits($str)
	{
		 return $str != null ? preg_replace('/\D/', '', $str) : null;
	}

	/**
	 * Sanitiza o valor em dinheiro BRL para o formato padrão.
	 * 
	 * @param string $str
	 * @return string or null
	 */
	public static function money($str)
	{
		$str = str_replace(' ', '', $str);
		$str = str_replace('.', '', $str);
		$str = str_replace(',', '.', $str);
		$str = str_replace('R$', '', $str);
		
		return (! empty($str) ? $str : null);	
	}

	/**
	 * Formata um nome passado:
	 * - Removendo todos espaços duplos ou mais.
	 * - Capitalizando as palavras com algumas exceções (da, do, das, dos, de).
	 * 
	 * @param string $str
	 * @return string
	 */
	public static function name(string $str)
	{
		$str = mb_strtolower($str, mb_detect_encoding($str));
		$str = trim(preg_replace('/\s+/', ' ', $str));

		return Helper::ucsentence($str, ['da', 'do', 'das', 'dos', 'de']);
	}

}
