<?php

namespace App\EventSubscriber;

use App\Exception\FormValidationException;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FormExceptionSubscriber implements EventSubscriberInterface {
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
			$response = $this->viewHandler->handle(View::create($exception->getForm()), $event->getRequest());
			$event->setResponse($response);
		}
	}
}
