<?php

namespace App\Compiler\Treaters;


use App\Helper;

class StructureTreater
{
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
				$lineClosingWhile = $this->getWhileClosingLineNumber(array_slice($lines, $key + 1, null, true));

				// Definindo as linhas que serão reunidas em uma só
				$linesToMerge = array_slice($lines, $key, $lineClosingWhile - $key + 1, true);
				$lines[$key]  = implode('', $linesToMerge);

				// Remover o primeiro item do array de elemebtos que devem ser removidos
				unset($linesToMerge[key($linesToMerge)]);

				// Removendo do array
				return array_values(Helper::remove($lines, array_keys($linesToMerge)));
			}

		}

		return $lines;
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