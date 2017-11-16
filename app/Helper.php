<?php
/**
 * Created by PhpStorm.
 * User: igorsgm
 * Date: 16/11/2017
 * Time: 01:10
 */

namespace App;

class Helper
{

	/* =======================================================================
	 *                              STRINGS
	 * =======================================================================
	 */

	/**
	 * Para verificar se uma string possui uma substring dentro dela
	 * (Case sensitive ou não)
	 *
	 * @param string $string        string que supostamente contém a substring
	 * @param string $subString     substring que está sendo procurada
	 * @param bool   $caseSensitive true para tratar as strings como case sensitive, false do contrário
	 *
	 * @return bool     true ser $string contém$subString
	 */
	public static function contains($string, $subString, $caseSensitive = true)
	{
		if (!$caseSensitive) {
			return stripos($string, $subString) !== false;
		}

		return strpos($string, $subString) !== false;
	}

	/**
	 * Verifica se uma string possui pelo menos um char em uppercase
	 *
	 * @param $string   string que será verificada
	 *
	 * @return int
	 */
	public static function hasUpperCaseChar($string)
	{
		return preg_match('/[A-Z]/', $string) > 0;
	}



	/* =======================================================================
	 *                              ARRAYS
	 * =======================================================================
	 */

	/**
	 * Remover múltiplos elementos em um array
	 *
	 * @param array $array            Array que terá os elementos removidos
	 * @param array $elementsToRemove Array com as keys que serão removidas
	 *
	 * @return array
	 */
	public static function remove($array, $elementsToRemove)
	{
		return array_diff_key($array, array_flip($elementsToRemove));
	}
}