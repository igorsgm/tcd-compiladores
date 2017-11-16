<?php

namespace App\Compiler;

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
	 * O Tradutor
	 * @var Translator $translator
	 */
	private $translator;

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
		$this->codeLined  = explode("\n", $this->codeInitialString);
		$this->translator = new Translator($this->codeLined);

		return $this->translator->execute();
	}

}