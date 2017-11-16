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
	 * Array do código sanitizado (dividido por linhas, sem comentários e sem espaços)
	 * @var array $codeSanitized
	 */
	private $codeSanitized;

	private $cHeader = [
		'#include <stdio.h>',
		'int main() {',
		'int a, b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, w, x, y, z;',
		'char str[512]; // auxiliar na leitura com G',
	];

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
		$this->codeLined     = explode("\n", $this->codeInitialString);
		$this->codeSanitized = $this->removeSpacesFromLines($this->codeLined);
	}

	/**
	 * Remove os espaços em branco de cada linha do código, bem como os "Enters" (new lines)
	 *
	 * @param array $lines
	 *
	 * @return array
	 */
	public function removeSpacesFromLines($lines)
	{
		foreach ($lines as $key => $line) {
			$lines[$key] = str_replace(' ', null, $line);

			// Se o último caracter for um "Enter", irá remove-lo
			if (ord(substr($lines[$key], -1)) == 13) {
				$lines[$key] = trim($lines[$key]);
			}
		}

		return $lines;
	}

}