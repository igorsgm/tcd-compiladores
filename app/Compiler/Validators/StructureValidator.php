<?php

namespace App\Compiler\Validators;


use App\Helper;

class StructureValidator
{

	/**
	 * Mapa de regras para cada uma das estruturas da LPS1
	 *
	 * maxLineLength é a quantidade máxima de caracteres que a linha deve possuir
	 * minLineLength é a quantidade mínima de caracteres que a linha deve possuir
	 * minCharsAfter é a quantidade mínima de caracteres que deve possuir após tal estrutura ter sido encontrada
	 * maxCharAfter é a quantidade máxima de caracteres que deve possuir após tal estrutura ter sido encontrada
	 * allLowerCase se todas os chars encontrados após a estrutura na linha deve ser do tipo lowercase
	 *
	 * @var array $lps1StructureRuleMap
	 */
	public $lps1StructureRuleMap = [
		'G' => [
			'maxLineLength' => 2,
			'minLineLength' => 2,
			'minCharsAfter' => 1,
			'maxCharsAfter' => 1,
			'allLowerCase'  => true,
		],
		'I' => [
			'maxLineLength' => 8,
			'minLineLength' => 4,
			'minCharsAfter' => 3,
			'maxCharsAfter' => 7,
			'allLowerCase'  => false
		],
		'W' => [
			'maxLineLength' => 5,
			'minLineLength' => 5,
			'minCharsAfter' => 4,
			'maxCharsAfter' => 4,
			'allLowerCase'  => true
		],
		'P' => [
			'maxLineLength' => 2,
			'minLineLength' => 2,
			'minCharsAfter' => 1,
			'maxCharsAfter' => 1,
			'allLowerCase'  => true
		]
	];

	public $linesWithError = [];


	public function validateStructures($codeSanitized)
	{
		$linesWithError = [];
		foreach ($codeSanitized as $lineNumber => $line) {
			if ($line != '}') {
				$structure = substr($line, 0, 1);

				// Se não passar na validação básica, irá inserir a lind ano $linesWithError
				if (!$this->basicValidator($structure, $line) || ($structure == 'I' && !$this->validateIf($line))) {
					$linesWithError[] = $lineNumber;
				}
			}
		}

		$this->linesWithError = $linesWithError;

		return empty($this->linesWithError);
	}

	public function basicValidator($structureElement, $line)
	{
		$structure = $this->lps1StructureRuleMap[$structureElement];

		if (strlen($line) > $structure['maxLineLength'] || strlen($line) < $structure['minLineLength']) {
			return false;
		}

		$lineWithoutStructure = substr($line, 1);

		if (strlen($lineWithoutStructure) > $structure['maxCharsAfter'] || strlen($lineWithoutStructure) < $structure['minCharsAfter']) {
			return false;
		}

		if ($structure['allLowerCase'] && Helper::hasUpperCaseChar($lineWithoutStructure)) {
			return false;
		}

		return true;

	}

	public function validateIf($line)
	{
		$lineWithoutStructure = substr($line, 1);

		$ifSentence = substr($lineWithoutStructure, 0, 3);
		$ifBody     = substr($lineWithoutStructure, 3);

		if ($this->isStructureSentence($ifBody)) {
			if (!$this->basicValidator(substr($line, 0, 1), $line)) {
				return false;
			}
		} else {
			$operationsValidator = new OperationValidator();

			if (!$operationsValidator->validateOperations([$ifBody])) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Verifica se a sentença é uma estrutura de $lps1StructureRuleMap
	 *
	 * @param array $line
	 *
	 * @return bool
	 */
	public function isStructureSentence($line)
	{
		return in_array(substr($line, 0, 1), array_keys($this->lps1StructureRuleMap));
	}

	/**
	 * @return mixed
	 */
	public function getLinesWithError()
	{
		return $this->linesWithError;
	}

}