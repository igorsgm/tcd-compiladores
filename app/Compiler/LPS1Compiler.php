<?php

namespace App\Compiler;

use App\Helper;

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
		$this->codeLined     = explode("\n", $this->codeInitialString);
		$this->codeSanitized = $this->removeSpacesFromLines($this->codeLined);
		$this->codeSanitized = $this->treatWhilesToCondenseInSingleLine($this->codeSanitized);

		return Translator::execute($this->codeSanitized);
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

	public function treatWhilesToCondenseInSingleLine($lines)
	{
		foreach ($lines as $key => $line) {

			// Se possuir um W { na linha $key, irá salvar o número da linha que possui o while e irá procurar aonde este W é fechado
			if (Helper::contains($line, 'W') && Helper::contains($line, '{')) {

				// Procurar a linha que o while fecha, envianddo como parametro os elementos que ainda faltam ser lidos
				$lineClosingWhile    = $this->getWhileClosingLineNumber(array_slice($lines, $key + 1, null, true));

				// Definindo as linhas que serão reunidas em uma só
				$linesToMerge = array_slice($lines, $key, $lineClosingWhile - $key + 1, true);
				$lines[$key]  = implode('', $linesToMerge);

				// Remover o primeiro item do array de elemebtos que devem ser removidos
				unset($linesToMerge[key($linesToMerge)]);

				// Removendo do array
				return array_values(Helper::remove($lines, array_keys($linesToMerge)));
			}

		}
	}

	/**
	 * Procura onde é fechado o de é fechado o While
	 *
	 * @param array $lines Um subarray das linahs do código (a partir de onde foi encontrado o "W")
	 *
	 * @return bool|int|string  false se não encontrar o fechamento do While, o número da linha que foi encontrado o fechamento dele.
	 */
	public function getWhileClosingLineNumber($lines)
	{
		foreach ($lines as $key => $line) {
			if (Helper::contains($line, '}')) {
				return $key;
			}
		}

		return false;
	}

}