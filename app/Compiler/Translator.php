<?php

namespace App\Compiler;


use App\Helper;

class Translator
{

	public $cHeader = [
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
	public $lps1Map = [
		'G' => '{|gets(str);|   sscanf(str, "%d", &:[0]);|}|', // :[0] => var1
		'I' => 'if (:[0]:[1]:[2]) {|  :[3]|}|', // :[0] => var1, :[1] => operacao ou comparação, :[2] => var2, :[3] => body
		'W' => 'while (:[0]:[1]:[2]) :[3]|   :[4]|:[5]|', // :[0] => var1, :[1] => operador, :[2] => var2, :[3] => {, :[4] => body, :[5] => }
		'P' => 'printf("%d\n", :[0]);|', // :[0] => var1,
		'#' => '!='
	];

	public $lps1Structures = ['G', 'I', 'W', 'P', '#'];

	/**
	 * Executará a tradução de cada uma das linhas do código em LPS1 para C
	 *
	 * @param array $codeSanitized
	 *
	 * @return array
	 */
	public function execute($codeSanitized)
	{
		$code = [];
		foreach ($codeSanitized as $keyLine => $lineElements) {
			$code[$keyLine] = self::replaceCharsOfLineByLps1Map(str_split($lineElements));
		}

		foreach ($code as $keyLine => $lineElements) {
			foreach ($lineElements as $keyElement => $element) {

				// Se aquele elemento faz parte do $lps1Map e possui ":[" para ser substituído
				if (in_array($element, $this->lps1Map) && Helper::contains($element, ':[')) {

					// Se não for um while
					if (!Helper::contains($element, 'while')) {

						// Quantidade de ":[x]" que devem ser substituídos
						$qtElementsToBeReplaced = substr_count($element, ':[');

						// Valores que devem ser substituídos, que é um array formado a partir $key atual do elemento + o número de "[]" (correspondentes aos elementos seguintes)
						$valuesToReplace = array_slice($lineElements, $keyElement + 1, $qtElementsToBeReplaced, true);

						$code[$keyLine][$keyElement] = $this->replaceElementBracketsWithValues($element, $valuesToReplace);

						// Removendo os elementos que já foram substituídos
						$code[$keyLine] = Helper::remove($code[$keyLine], array_keys($valuesToReplace));
					} else {

						$valuesToReplace = array_slice($lineElements, $keyElement + 1, 4, true);

						// Procurando a key do elemento que fecha a estrutura do while
						$closeWhileKeyElement = array_search('}', $lineElements);

						// Adicionando o "}" aos valores que serão substituídos
						$valuesToReplace[$closeWhileKeyElement] = $lineElements[$closeWhileKeyElement];

						$code[$keyLine][$keyElement] = $this->replaceElementBracketsWithValues($element, $valuesToReplace, [':[0]', ':[1]', ':[2]', ':[3]', ':[5]']);
						$code[$keyLine]              = Helper::remove($code[$keyLine], array_keys($valuesToReplace));

					}
				}
			}
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
	public function replaceCharsOfLineByLps1Map($lineElements)
	{
		foreach ($lineElements as $key => $char) {
			// Se estiver no array das estruturas da LPS1, irá substituir pelo correspondente no $lps1Map
			if (in_array($char, $this->lps1Structures)) {
				$lineElements[$key] = $this->lps1Map[$char];
			}

		}

		return $lineElements;
	}


	/**
	 * Substitui (bind) os ":[0]" elementos da string pelos $values
	 *
	 * @param string $element elemento que terá substituiçòes
	 * @param array  $values  Array com os valores que serão adicionados
	 *
	 * @return string
	 */
	public function replaceElementBracketsWithValues($element, $values, $arraySearch = [])
	{
		if (empty($arraySearch)) {
			// Montando o array das susbtrings que serão procuradas e serão substituídas
			foreach (array_values($values) as $key => $value) {
				$arraySearch[] = ':[' . $key . ']';
			}
		}

		return str_replace($arraySearch, $values, $element);
	}
}