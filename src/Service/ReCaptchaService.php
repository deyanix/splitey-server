<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReCaptchaService {
	private RequestStack $requestStack;
	private HttpClientInterface $client;

	public function __construct(HttpClientInterface $client, RequestStack $requestStack) {
		$this->client = $client;
		$this->requestStack = $requestStack;
	}

	public function getSiteKey(): string {
		return $_ENV['RECAPTCHA_SITE_KEY'];
	}

	public function getSecretKey(): string {
		return $_ENV['RECAPTCHA_SECRET_KEY'];
	}

	public function checkCaptcha(string $captcha, string $ip = null): bool {
		$request = $this->client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
			'body' => [
				'secret' => $this->getSecretKey(),
				'response' => $captcha,
				'remoteip' => $ip ?? $this->requestStack->getMainRequest()->getClientIp()
			]
		]);
		$response = $request->toArray();
		$success = $response['success'];
		if (is_bool($success)) {
			return $success;
		}
		return false;
	}
}
