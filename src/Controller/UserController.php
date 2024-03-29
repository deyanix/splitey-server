<?php

namespace App\Controller;

use App\Service\Controller\UserService;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Rest\Route('/users')]
#[OA\Tag(name: 'User')]
class UserController extends AbstractController {
	public function __construct(
		private readonly UserService $userService,
	) {	}

	#[Rest\Get(name: 'search')]
	#[Rest\View(statusCode: 200, serializerGroups: ['user:read'])]
	#[Rest\QueryParam("name")]
	#[OA\Get(summary: 'Search users')]
	public function searchUsers(string $name) {
		return $this->userService->searchUsers($name);
	}

	#[Rest\Get(path: '/current', name: 'get_current_user')]
	#[Rest\View(statusCode: 200, serializerGroups: ['user:read'])]
	#[OA\Get(summary: 'Get current user')]
	public function getCurrentUser() {
		return $this->userService->getCurrentUser();
	}

	#[Rest\Post("/change-email", name: 'change_email')]
	#[Rest\RequestParam('email', description: 'New email')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Change user an email')]
	public function changeEmail(string $email) {
		$this->userService->changeEmail($email);
	}
}
