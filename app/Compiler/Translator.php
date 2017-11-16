<?php

namespace App\Compiler;


class Translator
{
	/**
	 * Translator constructor.
	 *
	 * @param array $codeSanitized o array com os códigos em cada linha
	 */
	public function __construct($codeSanitized)
	{
		$code = [];
		foreach ($codeSanitized as $line) {
			$code[] = str_split($line);
		}

		var_dump($code);
		die;
	}
}