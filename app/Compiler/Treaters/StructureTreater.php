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

	/**
	 * Fazer a separação do body do if
	 *
	 * @param array $lines
	 *
	 * @return array
	 */
	public function treatIfSeparations($lines)
	{
		$codeLines = [];
		foreach ($lines as $key => $line) {
			if (substr($line, 0, 1) == 'I' && strlen($line) > 4) {
				$codeLines[] = substr($line, 0, 4);
				$codeLines[] = substr($line, 4) . ';';
			} else {
				$codeLines[] = $line;
			}
		}

		return $codeLines;
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

	/**
	 * Retorna o array de elementos que estão dentro do body do if
	 *
	 * @param array $lineElements Array de elementos
	 */
	public function getIfBodyElements($lineElements)
	{
//		var_dump($lineElements);
//		die;
		$ifBody = [];
		foreach ($lineElements as $key => $element) {
			if (Helper::contains($element, 'if')) {
				$bodySliceElements = array_slice($lineElements, $key + 1, 4, true);

				foreach ($bodySliceElements as $keySlice => $elementSlice) {
					if (!Helper::contains($elementSlice, ';') && !Helper::contains($elementSlice, '}')) {
						$ifBody[$keySlice] = $elementSlice;
					} else {
						if ($elementSlice == ';') {
							array_push($ifBody, $elementSlice);
						}

						return $ifBody;
					}

				}
			}
		}
	}

	/**
	 * Retorna o array de elementos que estão dentro do body do while
	 *
	 * @param array $lineElements Array de elementos
	 */
	public function getWhileBodyElements($lineElements)
	{
		$whileBody = [];
		foreach ($lineElements as $key => $element) {
			if (!Helper::contains($element, 'while') && $element != '}') {
				$whileBody[$key] = $element;
			}

			if ($element == '}') {
				return $whileBody;
			}
		}
	}

	/**
	 * Retorna as linhas pipelined daquelas operaçòes que estão de fora de alguma estrutura (I, G, W)
	 * Um dos últimos métodos chamados pelo Translator
	 *
	 * @param array $lines
	 *
	 * @return array
	 */
	public function treatAloneStructures($lines)
	{
		foreach ($lines as $key => $line) {
			if (count($line) > 1) {
				$lines[$key] = [$this->getRowPipeLined($line)];
			}
		}

		return $lines;
	}

	/**
	 * Retorna a string do corpo do while e também de qualquer array de elementos
	 *
	 * @param array $elements
	 */
	public function getRowPipeLined($elements)
	{
		$body = '';
		foreach ($elements as $element) {
			$body .= $element;

			if (in_array(substr($element, -1), [';', '}'])) {
				$body .= '|';
			}
		}

		return $body;
	}

	/**
	 * Retorna  o array com o code traduzido para C, cada linha em uma posição do array
	 *
	 * @param array $lines
	 *
	 * @return array
	 */
	public function separateCCodeStringsInLines($lines)
	{
		$codeCLines = [];
		foreach ($lines as $key => $line) {
			$codeCLines[] = explode('|', $line);
		}

		return $codeCLines;
	}

	/**
	 * Gera a string HTML que será exibida no console do browser
	 *
	 * @param array $codeStructures
	 */
	public function optimizeCCodeToHtml($codeStructures)
	{
		$cString = '<span class="code-success">';
		foreach ($codeStructures as $codeLines) {
			//  Remove todos null/empty/false do array.
			$codeLines = array_filter($codeLines);

			foreach ($codeLines as $line) {
				$cString .= $line . '<br>';
			}
		}

		return $cString . '</span>';
	}
}