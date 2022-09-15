<?php

namespace App\Service\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UserService {
	private UserRepository $userRepository;
	private Security $security;

	public function __construct(
		EntityManagerInterface $entityManager,
		Security $security
	) {
		$this->userRepository = $entityManager->getRepository(User::class);
		$this->security = $security;
	}

	public function getCurrentUser(): ?User {
		$user = $this->security->getUser();
		if ($user instanceof User) {
			return $user;
		}
		return null;
	}

	public function getUserByLogin(string $login): ?User {
		return $this->userRepository->findByUsernameOrEmail($login);
	}

	public function getUserByUsername(string $username): ?User {
		return $this->userRepository->findOneBy(['username' => $username]);
	}

	public function getUserByEmail(string $email): ?User {
		return $this->userRepository->findOneBy(['email' => $email]);
	}
}
