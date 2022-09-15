<?php

namespace App\Service;

use Exception;

class RandomizerService {
	/**
	 * @throws Exception
	 */
	public function getBytes(int $length): string {
		return random_bytes($length);
	}

	/**
	 * @param int $length Token length
	 *
	 * @return string A random token
	 *
	 * @throws Exception
	 */
	public function getString(int $length): string {
		$bytes = ceil($length / 4) * 3;
		$unescapedToken = base64_encode($this->getBytes($bytes));
		$token = strtr($unescapedToken, '+/', '-_');

		return substr($token, 0, $length);
	}
}
