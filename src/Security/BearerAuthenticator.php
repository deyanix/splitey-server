<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Service\Security\AccessTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class BearerAuthenticator extends AbstractAuthenticator {

	public function __construct(
		private readonly AccessTokenService $accessTokenService,
		private readonly UserRepository $userRepository,
	) {}

	public function supports(Request $request): bool {
		return $request->headers->has('Authorization');
	}

	public function authenticate(Request $request): Passport {
		$authorization = $request->headers->get('Authorization');
		if (!$authorization) {
			throw new CustomUserMessageAuthenticationException('No API token provided.');
		}

		preg_match('/Bearer (.*)/', $authorization, $authorizationMatches);
		$apiToken = $authorizationMatches[1];
		if (!$apiToken) {
			throw new CustomUserMessageAuthenticationException('No API token provided.');
		}

		if (!$this->accessTokenService->validateToken($apiToken)) {
			throw new CustomUserMessageAuthenticationException('Invalid API token provided.');
		}

		$token = $this->accessTokenService->parseToken($apiToken);

		$subject = $token->claims()->get("sub");
		return new SelfValidatingPassport(new UserBadge($subject, function ($userIdentifier) {
			return $this->userRepository->findOneBy(['id' => $userIdentifier]);
		}));
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
		return null;
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
		$data = [
			'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
		];

		return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
	}
}
