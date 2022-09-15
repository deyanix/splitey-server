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

#[Rest\Route('/account', name: 'account_')]
#[OA\Tag(name: 'Account')]
#[Nelmio\Security(name: null)]
class AccountController extends AbstractController{
	public function __construct(
		private readonly FormService $formService,
		private readonly UserService $userService,
		private readonly CreateAccountService $accountService,
		private readonly ResetPasswordService $resetPasswordService,
	) {	}

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
	public function createAccount(Request $request) {
		$form = $this->formService->handle($request, CreateAccountForm::class);
		$this->accountService->createAccount($form->getData());
	}

	#[Rest\View(statusCode: 200)]
	#[Rest\Post('/reset-password', name: 'reset_password')]
	#[OA\Post(summary: 'Send mail with instruction', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'email',
			type: 'string'
		),
		new OA\Property(
			property: 'captcha',
			type: 'string'
		)],
	)))]
	public function resetPassword(Request $request) {
		$form = $this->formService->handle($request, ResetPasswordForm::class);
		$this->resetPasswordService->resetPassword($form->getData());
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
	public function authorizedResetPassword(Request $request) {
		$form = $this->formService->handle($request, AuthorizedResetPasswordForm::class);
		$this->resetPasswordService->authorizedResetPassword($form->getData());
	}

	#[Rest\Post("/resend-activation", name: 'resend_activation')]
	#[Rest\RequestParam('login', description: 'Name or email of the user')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Resend a mail with activation token')]
	public function resendActivation(string $login) {
		$user = $this->userService->getUserByLogin($login);
		$this->accountService->resendActivation($user);
	}

	#[Rest\Post("/activate", name: 'activate')]
	#[Rest\RequestParam('token', description: 'Activation token sent to email')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Activate an account via token')]
	public function activate(string $token) {
		$this->accountService->activate($token);
	}

	#[Rest\Post("/confirm-email", name: 'confirm_email')]
	#[Rest\RequestParam('token', description: 'Confirmation token sent to email')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Confirm an email via token')]
	public function confirmEmail(string $token) {
		$this->accountService->confirmEmail($token);
	}
}
