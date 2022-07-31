<?php

namespace App\EventSubscriber;

use App\Exception\FormValidationException;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface {
	public static function getSubscribedEvents(): array {
		return [KernelEvents::EXCEPTION => ['onException']];
	}

	private ViewHandlerInterface $viewHandler;

	public function __construct(ViewHandlerInterface $viewHandler) {
		$this->viewHandler = $viewHandler;
	}

	public function onException(ExceptionEvent $event): void {
		$exception = $event->getThrowable();
		if ($exception instanceof FormValidationException) {
			$view = View::create($exception->getForm());
		} else {
			$errors = [];
			$currentException = $exception;
			while ($currentException !== null) {
				$errors[] = $currentException;
				$currentException = $currentException->getPrevious();
			}

			$view = View::create([
				'errors' => array_map(fn ($exception) => [
					'message' => $exception->getMessage(),
					'file' => $exception->getFile(),
					'line' => $exception->getLine(),
					'exception' => get_class($exception)
				], $errors)
			]);
		}

		$response = $this->viewHandler->handle($view, $event->getRequest());
		$event->setResponse($response);
	}
}
