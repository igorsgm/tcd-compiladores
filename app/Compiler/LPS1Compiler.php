<?php

namespace App\Compiler;

use App\Compiler\Treaters\StructureTreater;

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
	 * LPS1Compiler constructor.
	 *
	 * @param string $code
	 */
	public function __construct($code)
	{
		$this->codeInitialString = $code;
	}

	public function run()
	{
		$this->codeLined = explode("\n", $this->codeInitialString);

		$treater = new StructureTreater();

		$this->codeSanitized = $treater->removeSpacesFromLines($this->codeLined);
		$this->codeSanitized = $treater->treatWhilesToCondenseInSingleLine($this->codeSanitized);

		return Translator::execute($this->codeSanitized);
	}

}