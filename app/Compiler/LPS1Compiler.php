<?php

namespace App\Compiler;

use App\Compiler\Treaters\StructureTreater;
use App\Compiler\Validators\OperationValidator;
use App\Compiler\Validators\StructureValidator;

class LPS1Compiler
{
	/**
	 * Código inicial que o usuário forneceu
	 * @var string $codeInitialString
	 */
	private $codeInitialString;

	/**
	 * Array do código fornecido, dividido por linhas, sem comentários.
	 * Caso seja encontrado algum erro no processo de compilação, este atributo será utilizado
	 * para adicionar os erros em cada uma de suas linhas
	 *
	 * @var array $codeLined
	 */
	private $codeLined;

	/**
	 * Array do código sanitizado (dividido por linhas, sem comentários e sem espaços)
	 * @var array $codeSanitized
	 */
	private $codeSanitized;

	/**
	 * Objeto da classe que faz o tratamento estrutural do código
	 * @var StructureTreater StructureTreater
	 */
	private $structureTreater;

	/**
	 * O Tradutor
	 * @var Translator $translator
	 */
	private $translator;

	/**
	 * O validador do código, que verifica se a sintaxe está correta antes de executar a tradução
	 * Retorna os erros se for necessário
	 * @var StructureValidator $structureValidator
	 */
	private $structureValidator;

	private $linesWithError;

	/**
	 * LPS1Compiler constructor.
	 *
	 * @param string $code
	 */
	public function __construct($code)
	{
		$this->codeInitialString  = $code;
		$this->structureTreater   = new StructureTreater();
		$this->structureValidator = new StructureValidator();
		$this->operationValidator = new OperationValidator();
	}

	public function execute()
	{
		$this->initialSanitize();

		$codeLinesByValidator = $this->getSeparatedLinesByValidator($this->codeSanitized);

		if (!$this->structureValidator->validateStructures($codeLinesByValidator['structure'])
			|| !$this->operationValidator->validateOperations($codeLinesByValidator['operation'])) {

			$this->linesWithError = $this->structureValidator->getLinesWithError() +  $this->operationValidator->getLinesWithError();

			return $this->structureTreater->getLPS1CodeErrorHtml($this->codeLined, $this->linesWithError);
		}


		$this->translator = new Translator($this->codeSanitized, $this->structureTreater);

		return $this->translator->execute();
	}

	/**
	 * Seta o codeLined e o codeSanitized, realizando os code sanitizes iniciais
	 */
	public function initialSanitize()
	{
		$this->codeLined     = explode("\n", $this->codeInitialString);
		$this->codeSanitized = $this->structureTreater->removeSpacesFromLines($this->codeLined);
	}

	/**
	 * Separar em dois arrays diferentes de acordo com o tipo de Validator
	 * que será utilizado para a análise sintática de tais linhas.
	 *
	 * @param array $codeSanitized
	 *
	 * @return array    No formato ['structure' => [], 'operation' => ''];
	 */
	public function getSeparatedLinesByValidator($codeSanitized)
	{
		$code = [
			'structure' => [],
			'operation' => []
		];

		foreach ($codeSanitized as $lineNumber => $line) {
			$firstChar = substr($line, 0, 1);

			if (in_array($firstChar, ['G', 'P', 'I', 'W', '}'])) {
				$code['structure'][$lineNumber] = $line;
			} else {
				$code['operation'][$lineNumber] = $line;
			}
		}

		return $code;
	}

}