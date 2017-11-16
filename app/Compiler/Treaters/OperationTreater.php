<?php

namespace App\Compiler\Treaters;


class OperationTreater
{

	public $mathOperationsMap = [
		'+', '-', '*', '/', '%'
	];

	public $comparisonOperationsMap = [
		'=', '<', "#"
	];


	public function treatOperations($codeSanitized)
	{
		foreach ($codeSanitized as $lineNumber => $line) {
			$firstChar = substr($line, 0, 1);

			if (in_array($firstChar, $this->mathOperationsMap)) {
				$codeSanitized[$lineNumber] = $this->formatMathOperation($line) . ';';

			} elseif (in_array($firstChar, $this->comparisonOperationsMap)) {
				$codeSanitized[$lineNumber] = $this->formatComparisonOperation($line) . ';';
			}
		}

		return $codeSanitized;
	}

	/**
	 * Formatação dos operadores matemáticos (pertencentes ao $mathOperationsMap(
	 *
	 * @param string $line  A linha do código com a operacão matemática
	 *
	 * @return string
	 */
	public function formatMathOperation($line)
	{
		$elements = str_split($line);

		return $elements[1] . '=' . $elements[2] . $elements[0] . $elements[3];
	}

	/**
	 * Formatação dos operadores de comparação/atribuição (pertencentes ao $comparisonOperationsMap)
	 *
	 * @param string $line  A linha do código com a operação de comparaçõa/atribuição
	 *
	 * @return string
	 */
	public function formatComparisonOperation($line)
	{
		$elements = str_split($line);

		return $elements[1] . $elements[0] . $elements[2];
	}

	/**
	 * Tratamento das operaçòes na linha, para evitar que quebre o html da exibição
	 *
	 * @param string $line
	 *
	 * @return string
	 */
	public static function formatOperatorsHtmlInLine($line)
	{
		return str_replace(['<', '=', '!='], [' < ', ' = ', ' != '], $line);
	}
}