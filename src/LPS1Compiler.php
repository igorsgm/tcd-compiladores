<?php

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


	public function __construct($code)
	{
		$this->codeInitialString = $code;
	}

	public function run()
	{
		$this->codeLined     = explode("\n", $this->codeInitialString);
		$this->codeSanitized = explode("\n", $this->sanitizeCode($this->codeInitialString));

		return $this->codeSanitized;
	}

	/**
	 * Irá limpar os comentários do código e remover os espaços, para a análise de cada uma das linhas do código
	 *
	 * @param string $code
	 *
	 * @return string
	 */
	public function sanitizeCode($code)
	{
		// Remover comentários do tipo /* */
		$code = preg_replace('!/\*.*?\*/!s', '', $code);

		// Remover comentários do tipo //
		$code = preg_replace('~(?:#|//)[^\r\n]*|/\*.*?\*/~s', '', $code);

		// Remover espaços
		$code = str_replace(' ', '', $code);

		return $code;
	}


}