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

	public function formatMathOperation($line)
	{
		$elements = str_split($line);

		return $elements[1] . '=' . $elements[2] . $elements[0] . $elements[3];
	}

	public function formatComparisonOperation($line)
	{
		$elements = str_split($line);

		return $elements[1] . $elements[0] . $elements[2];
	}
}