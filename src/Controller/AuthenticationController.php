<?php

namespace App\Controller;

use App\Model\LoginResult;
use App\Service\Controller\AuthenticationService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Rest\Route('/auth', name: 'auth_')]
#[OA\Tag(name: 'Authentication')]
#[Nelmio\Security(name: null)]
class AuthenticationController extends AbstractController {
	public function __construct(
		private readonly AuthenticationService $authenticationService,
	) {	}

	#[Rest\Post("/login", name: 'login')]
	#[Rest\RequestParam('login', description: 'Name or email of the user')]
	#[Rest\RequestParam('password', description: 'The password in plain text')]
	#[Rest\RequestParam('deviceUuid', description: 'UUID', nullable: true)]
	#[Rest\RequestParam('rememberMe', default: false)]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Get authentication tokens')]
	public function login(string $login, string $password, ?string $deviceUuid, bool $rememberMe): LoginResult {
		$user = $this->authenticationService->authenticate($login, $password);
		return $this->authenticationService->login($user, $deviceUuid, $rememberMe);
	}

	#[Rest\Post('/refresh', name: 'refresh')]
	#[Rest\RequestParam('refreshToken')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(
		summary: 'Refresh authentication tokens'
	)]
	public function refresh(string $refreshToken) {
		$token = $this->authenticationService->getRefreshToken($refreshToken);
		return $this->authenticationService->refresh($token);
	}

	#[Rest\View(statusCode: 200)]
	#[Rest\Post('/invalidate', name: 'invalidate')]
	#[Rest\RequestParam('refreshToken')]
	#[OA\Post(summary: 'Invalid refresh tokens')]
	public function invalidate(string $refreshToken) {
		$token = $this->authenticationService->getRefreshToken($refreshToken);
		$this->authenticationService->invalidate($token);
	}
}
