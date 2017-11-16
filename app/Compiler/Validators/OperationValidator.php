<?php

namespace App\Compiler\Validators;


use App\Helper;

class OperationValidator
{
	/**
	 * Mapa de regras para cada uma das operações
	 *
	 * maxLineLength é a quantidade máxima de caracteres que a linha deve possuir
	 * minLineLength é a quantidade mínima de caracteres que a linha deve possuir
	 *
	 * @var array $lps1OperationRuleMap
	 */
	public $lps1OperationRuleMap = [
		'=' => [
			'maxLineLength' => 3,
			'minLineLength' => 3,
		],
		'<' => [
			'maxLineLength' => 3,
			'minLineLength' => 3,
		],
		'#' => [
			'maxLineLength' => 3,
			'minLineLength' => 3,
		],
		'%' => [
			'maxLineLength' => 4,
			'minLineLength' => 4,
		],
		'+' => [
			'maxLineLength' => 4,
			'minLineLength' => 4,
		],
		'*' => [
			'maxLineLength' => 4,
			'minLineLength' => 4,
		],
		'-' => [
			'maxLineLength' => 4,
			'minLineLength' => 4,
		],
		'/' => [
			'maxLineLength' => 4,
			'minLineLength' => 4,
		]
	];

	public $linesWithError = [];

	public function validateOperations($codeSanitized)
	{
		$linesWithError = [];
		foreach ($codeSanitized as $lineNumber => $line) {
			if ($line != '}') {

				if ($operation = $this->getOperation($line)) {
					// Se não passar na validação básica, irá inserir a lind ano $linesWithError

					if (!$this->basicValidator($operation, $line)) {
						$linesWithError[] = $lineNumber;
					}

					// Se possuir uma operação não válida, irá inserir a linha como erro
				} else {
					$linesWithError[] = $lineNumber;
				}
			}
		}

		$this->linesWithError = $linesWithError;

		return empty($this->linesWithError);
	}

	public function basicValidator($operationElement, $line)
	{
		$lineWithoutOperator = $this->getLineWithoutOperator($line);

		$operatorRules = $this->lps1OperationRuleMap[$operationElement];

		if (strlen($line) > $operatorRules['maxLineLength'] || strlen($line) < $operatorRules['minLineLength']) {
			return false;
		}

		if (Helper::hasUpperCaseChar($lineWithoutOperator)) {
			return false;
		}

		return true;

	}

	/**
	 * Retorna a operação de uma sentença e false se não possuir alguma operação
	 *
	 * @param string $line A linha com a operação
	 *
	 * @return bool|string|integer
	 */
	public function getOperation($line)
	{
		preg_match('/[^\da-z]/i', $line, $operations);

		if (empty($operations) || !in_array($operations[0], array_keys($this->lps1OperationRuleMap))) {
			return false;
		}

		return $operations[0];
	}

	public function getLineWithoutOperator($line)
	{
		return preg_replace('/[^\da-z]/i', '', $line);
	}

	/**
	 * @return mixed
	 */
	public function getLinesWithError()
	{
		return $this->linesWithError;
	}
}