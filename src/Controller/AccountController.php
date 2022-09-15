<?php

namespace App\Controller;


use App\Form\AuthorizedResetPasswordForm;
use App\Form\CreateAccountForm;
use App\Form\ResetPasswordForm;
use App\Service\Controller\CreateAccountService;
use App\Service\Controller\ResetPasswordService;
use App\Service\Controller\UserService;
use App\Service\FormService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Rest\Route('/account')]
#[OA\Tag(name: 'Account')]
#[Nelmio\Security(name: null)]
class AccountController extends AbstractController{
	private FormService $formService;
	private UserService $userService;

	public function __construct(
		UserService $userService,
		FormService $formService
	) {
		$this->userService = $userService;
		$this->formService = $formService;
	}

	#[Rest\View(statusCode: 200)]
	#[Rest\Post(name: 'create_account')]
	#[OA\Post(summary: 'Create an account', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'firstName',
			type: 'string'
		),
		new OA\Property(
			property: 'lastName',
			type: 'string'
		),
		new OA\Property(
			property: 'username',
			type: 'string'
		),
		new OA\Property(
			property: 'email',
			type: 'string'
		),
		new OA\Property(
			property: 'password',
			type: 'string'
		),
		new OA\Property(
			property: 'captcha',
			type: 'string'
		)],
	)))]
	public function createAccount(Request $request, CreateAccountService $service) {
		$form = $this->formService->handle($request, CreateAccountForm::class);
		$service->createAccount($form->getData());
	}

	#[Rest\View(statusCode: 200)]
	#[Rest\Post('/reset-password', name: 'reset_password')]
	#[OA\Post(summary: 'Send mail with instruction', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'email',
			type: 'string'
		)],
	)))]
	public function resetPassword(Request $request, ResetPasswordService $service) {
		$form = $this->formService->handle($request, ResetPasswordForm::class);
		$service->resetPassword($form->getData());
	}

	#[Rest\View(statusCode: 200)]
	#[Rest\Put('/reset-password', name: 'authorized_reset_password')]
	#[OA\Put(summary: 'Reset password via token', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'token',
			type: 'string'
		),
		new OA\Property(
			property: 'password',
			type: 'string'
		)],
	)))]
	public function authorizedResetPassword(Request $request, ResetPasswordService $service) {
		$form = $this->formService->handle($request, AuthorizedResetPasswordForm::class);
		$service->authorizedResetPassword($form->getData());
	}

	#[Rest\Post("/resend-confirmation")]
	#[Rest\RequestParam('login', description: 'Name or email of the user')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Resend a mail with activation token')]
	public function resendConfirmation(string $login, CreateAccountService $service) {
		$user = $this->userService->getUserByLogin($login);
		$service->resend($user);
	}

	#[Rest\Post("/confirm-email")]
	#[Rest\RequestParam('token', description: 'Confirmation token sent to email')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Confirm account via token')]
	public function confirmEmail(string $login, CreateAccountService $service) {
		// TODO: Do it!
	}
}
