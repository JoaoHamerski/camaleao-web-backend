<?php 	

namespace App\Util;

class Validate {

	/**
	 * Verifica se a string passada é uma data válida em formato (dd/mm/aaaa)
	 * 
	 * @param string $date
	 * @return bool
	 */
	public static function isDate($date)
	{
		return preg_match('/^[0-9]{2}[-|\/]{1}[0-9]{2}[-|\/]{1}[0-9]{4}$/', $date);
	}
}