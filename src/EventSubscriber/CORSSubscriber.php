<?php

namespace App\EventSubscriber;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CORSSubscriber implements EventSubscriberInterface {
	#[ArrayShape([
		KernelEvents::RESPONSE => 'string[]',
		KernelEvents::REQUEST => 'array'
	])]
	public static function getSubscribedEvents(): array {
		return [
			KernelEvents::RESPONSE => ['onResponse'],
			KernelEvents::REQUEST => ['onRequest', 255]
		];
	}

	private ?array $trustedOrigins = null;

	public function __construct() {
		$trustedOriginsEnv = $_ENV['APP_TRUSTED_ORIGIN'];
		if (!empty($trustedOriginsEnv)) {
			$this->trustedOrigins = explode(',', $trustedOriginsEnv);
		}
	}

	private function checkOrigin(?string $origin): bool {
		return !empty($origin) && (empty($this->trustedOrigins) || in_array($origin, $this->trustedOrigins));
	}

	public function onRequest(RequestEvent $event): void {
		$request = $event->getRequest();
		$origin = $request->headers->get('Origin');
		if ($this->checkOrigin($origin) && $request->getMethod() === 'OPTIONS') {
			$response = new Response();
			$response->headers->set('Accept', 'application/json');
			$response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
			$event->setResponse($response);
		}
	}

	public function onResponse(ResponseEvent $event): void {
		$origin = $event->getRequest()->headers->get('Origin');
		if ($this->checkOrigin($origin)) {
			$response = $event->getResponse();
			$response->headers->set('Access-Control-Allow-Origin', $origin);
			$response->headers->set('Access-Control-Allow-Credentials', 'true');
			$response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD');
		}
	}
}

