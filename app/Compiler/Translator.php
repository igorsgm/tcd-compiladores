<?php

namespace App\Compiler;


use App\Compiler\Treaters\OperationTreater;
use App\Compiler\Treaters\StructureTreater;
use App\Helper;

class Translator
{

	/**
	 * Array do código sanitizado (dividido por linhas, sem comentários e sem espaços)
	 * @var array $codeSanitized
	 */
	private $codeSanitized;

	/**
	 * Array do código traduzido, a ser retornado
	 * @var array $codeTranslated
	 */
	private $codeTranslated;

	/**
	 * Objeto da classe que faz o tratamento estrutural do código
	 * @var StructureTreater StructureTreater
	 */
	private $structureTreater;

	/**
	 * Objeto da classe que faz o tratamento das operações do código
	 * @var OperationTreater $operationTreater
	 */
	private $operationTreater;

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
		'G' => '{|gets(str);| sscanf(str, "%d", &:[0]);|}|', // :[0] => var1
		'I' => 'if (:[0]:[1]:[2]) {|:[3]|}|', // :[0] => var1, :[1] => operacao ou comparação, :[2] => var2, :[3] => body
		'W' => 'while (:[0]:[1]:[2]) :[3]|:[4]:[5]|', // :[0] => var1, :[1] => operador, :[2] => var2, :[3] => {, :[4] => body, :[5] => }
		'P' => 'printf("%d\n", :[0]);|', // :[0] => var1,
		'#' => '!='
	];

	public $lps1Structures = ['G', 'I', 'W', 'P', '#'];


	public function __construct($codeSanitized, $structureTreater)
	{
		$this->structureTreater = $structureTreater;
		$this->operationTreater = new OperationTreater();

		$this->codeSanitized    = $codeSanitized;

		$this->codeTranslated = $this->structureTreater->treatIfSeparations($this->codeSanitized);
		$this->codeTranslated = $this->operationTreater->treatOperations($this->codeTranslated);
		$this->codeTranslated = $this->structureTreater->treatWhilesToCondenseInSingleLine($this->codeTranslated);
	}

	/**
	 * Executará a tradução de cada uma das linhas do código em LPS1 para C
	 *
	 * @return string
	 */
	public function execute()
	{
		$code = [];
		foreach ($this->codeTranslated as $keyLine => $lineElements) {
			$code[$keyLine] = self::replaceCharsOfLineByLps1Map(str_split($lineElements));
		}

		$code = $this->translateSimpleStructures($code);
		$code = $this->translateIfs(array_filter($code));
		$code = $this->translateWhiles(array_filter($code));
		$code = $this->structureTreater->treatAloneStructures(array_filter($code));
		$code = $this->structureTreater->separateCCodeStringsInLines(array_column($code, '0'));

		// Adicionando o header padrão do C
		array_unshift($code, $this->cHeader);

		return $this->structureTreater->optimizeCCodeToHtml($code);
	}

	public function translateSimpleStructures($code)
	{
		foreach ($code as $keyLine => $lineElements) {
			foreach ($lineElements as $keyElement => $element) {

				// Se aquele elemento faz parte do $lps1Map e possui ":[" para ser substituído
				if (in_array($element, $this->lps1Map) && Helper::contains($element, ':[') && !Helper::contains($element, 'while') && !Helper::contains($element, 'if')) {

					// Quantidade de ":[x]" que devem ser substituídos
					$qtElementsToBeReplaced = substr_count($element, ':[');

					// Valores que devem ser substituídos, que é um array formado a partir $key atual do elemento + o número de "[]" (correspondentes aos elementos seguintes)
					$valuesToReplace = array_slice($lineElements, $keyElement + 1, $qtElementsToBeReplaced, true);

					$code[$keyLine][$keyElement] = $this->replaceElementBracketsWithValues($element, $valuesToReplace);

					// Removendo os elementos que já foram substituídos
					$code[$keyLine] = Helper::remove($code[$keyLine], array_keys($valuesToReplace));
				}
			}
		}

		return $code;
	}


	public function translateIfs($code)
	{
		foreach ($code as $keyLine => $lineElements) {
			foreach ($lineElements as $keyElement => $element) {

				if (in_array($element, $this->lps1Map) && Helper::contains($element, ':[') && Helper::contains($element, 'if') && !Helper::contains($element, 'while')) {


					$valuesToReplace = array_slice($lineElements, $keyElement + 1, 3, true);

					$code[$keyLine][$keyElement] = $this->replaceElementBracketsWithValues($element, $valuesToReplace, [':[0]', ':[1]', ':[2]']);
					$code[$keyLine]              = Helper::remove($code[$keyLine], array_keys($valuesToReplace));

					// Se o if for uma estrutura simples, ou seja, não estiver dentro de um while
					if (count($lineElements) == 4) {
						array_push($code[$keyLine], $code[$keyLine + 1][0]);
						unset($code[$keyLine + 1][0]);

						$valuesToReplace = [];
						array_push($valuesToReplace, $code[$keyLine][1]);

						$code[$keyLine][$keyElement] = $this->replaceElementBracketsWithValues($code[$keyLine][$keyElement], $valuesToReplace, [':[3]']);
						$code[$keyLine]              = Helper::remove($code[$keyLine], [1]);
					} else {
						$valuesToReplace = [];
						$ifBodyElements  = $this->structureTreater->getIfBodyElements($code[$keyLine]);

						array_push($valuesToReplace, $this->structureTreater->getRowPipeLined($ifBodyElements));

						$code[$keyLine][$keyElement] = $this->replaceElementBracketsWithValues($code[$keyLine][$keyElement], $valuesToReplace, [':[3]']);
						$code[$keyLine]              = Helper::remove($code[$keyLine], array_keys($ifBodyElements));
					}
				}
			}
		}

		return $code;
	}

	public function translateWhiles($code)
	{
		foreach ($code as $keyLine => $lineElements) {
			foreach ($lineElements as $keyElement => $element) {

				if (in_array($element, $this->lps1Map) && Helper::contains($element, ':[') && Helper::contains($element, 'while') && !Helper::contains($element, 'if')) {

					$valuesToReplace = array_slice($lineElements, $keyElement + 1, 4, true);

					$code[$keyLine][$keyElement] = $this->replaceElementBracketsWithValues($element, $valuesToReplace, [':[0]', ':[1]', ':[2]', ':[3]']);
					$code[$keyLine]              = Helper::remove($code[$keyLine], array_keys($valuesToReplace));

					$valuesToReplace = [];

					$whileBodyElements = $this->structureTreater->getWhileBodyElements($code[$keyLine]);

					// Keys que serão removidas do array deste elemento
					$whileBodyKeys = array_keys($whileBodyElements);

					array_push($valuesToReplace, $this->structureTreater->getRowPipeLined($whileBodyElements));

					// Procurando a key do elemento que fecha a estrutura do while
					$closeWhileKeyElement = array_search('}', $lineElements);

					array_push($whileBodyKeys, $closeWhileKeyElement);

					// Adicionando o "}" aos valores que serão substituídos
					array_push($valuesToReplace, $lineElements[$closeWhileKeyElement]);

					$code[$keyLine][$keyElement] = $this->replaceElementBracketsWithValues($code[$keyLine][$keyElement], $valuesToReplace, [':[4]', ':[5]']);
					$code[$keyLine]              = Helper::remove($code[$keyLine], $whileBodyKeys);
				}
			}
		}

		return $code;
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