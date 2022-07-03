<?php

namespace App\Service\Security;

use Exception;

class RandomizerService {
	private const AVAILABLE_CHARS = '0123456789abcdefABCDEF';

	/**
	 * @throws Exception
	 */
	public function getRandomBytes(int $length): string {
		return random_bytes($length);
	}

	public function getRandomString(int $length): string {
		$charsLength = strlen(self::AVAILABLE_CHARS);
		$result = '';
		for ($i = 0; $i < $length; $i++) {
			$result .= self::AVAILABLE_CHARS[mt_rand(0, $charsLength - 1)];
		}
		return $result;
	}
}
