<?php

namespace App\Compiler;


class Translator
{

	public static $cHeader = [
		'#include <stdio.h>',
		'int main() {',
		'int a, b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, w, x, y, z;',
		'char str[512]; // auxiliar na leitura com G',
	];

	/**
	 * Mapa de substituições da linguage LPS1 para C.
	 * Os valores "|" significam uma quebra de linha e os "[]" um valor a ser inserido ali dentro.
	 *
	 * @var array $lps1Map
	 */
	public static $lps1Map = [
		'G' => '{|gets(str);|   sscanf(str, "%d", &[]);|}|', // [1] => var1
		'I' => 'if ([][][]) {|  []|}|', // [1] => var1, [2] => operacao ou comparação, [3] => var2, [4] => body
		'W' => 'while ([][][]) []|   []|[]|', // [1] => var1, [2] => operador, [3] => var2, [4] => {, [5] => body, [6] => }
		'P' => 'printf("%d\n", []);|', // [1] => var1,
		'#' => '!='
	];

	public static $lps1Structures = ['G', 'I', 'W', 'P', '#'];

	/**
	 * Executará a tradução de cada uma das linhas do código em LPS1 para C
	 *
	 * @param array $codeSanitized
	 *
	 * @return array
	 */
	public static function execute($codeSanitized)
	{
		$code = [];
		foreach ($codeSanitized as $lineNumber => $lineElements) {
			$code[$lineNumber] = self::replaceCharsOfLineByLps1Map(str_split($lineElements));
		}

		var_dump($code);
		die;
	}

	/**
	 * Efetua  substituição do token em LPS1 para o Token em C
	 *
	 * @param array $lineElements
	 *
	 * @return array
	 */
	public static function replaceCharsOfLineByLps1Map($lineElements)
	{
		foreach ($lineElements as $key => $char) {
			// Se estiver no array das estruturas da LPS1, irá substituir pelo correspondente no $lps1Map
			if (in_array($char, self::$lps1Structures)) {
				$lineElements[$key] = self::$lps1Map[$char];
			}

		}

		return $lineElements;
	}
}