<?php

namespace App\Service\Security;

use App\Entity\User;
use DateTimeImmutable;
use DateTimeZone;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;

class AccessTokenService {
	private Configuration $configuration;
	private string $host;

	/**
	 * The default constructor that creates the configuration and sets constraints up.
	 */
	public function __construct(string $host) {
		$this->host = $host;
		$this->configuration = Configuration::forAsymmetricSigner(
			new Signer\Rsa\Sha512(),
			InMemory::file(join(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'config', 'jwt', 'private.pem'])),
			InMemory::file(join(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'config', 'jwt', 'public.pem']))
		);
		$this->configuration->setValidationConstraints(
			new Constraint\IssuedBy($host),
			new Constraint\IdentifiedBy($_ENV['JWT_ID']),
			new Constraint\PermittedFor($host),
			new Constraint\SignedWith($this->getConfiguration()->signer(),
				$this->getConfiguration()->verificationKey()),
			new Constraint\StrictValidAt(new SystemClock(new DateTimeZone('+1')))
		);
	}

	/**
	 * Allows retrieving the configuration to use with JWT tokens.
	 *
	 * @return Configuration The prepared configuration
	 */
	public function getConfiguration(): Configuration {
		return $this->configuration;
	}

	/**
	 * Creates the access token with matching valid data.
	 *
	 * @param User $user The user for whom the token is being issued.
	 *
	 * @return Token\Plain The JWT token.
	 */
	public function createToken(User $user): Token\Plain {
		$now = new DateTimeImmutable();
		return $this->getConfiguration()->builder()
			->issuedBy($this->host)
			->permittedFor($this->host)
			->identifiedBy($_ENV['JWT_ID'])
			->issuedAt($now)
			->relatedTo($user->getId())
			->canOnlyBeUsedAfter($now)
			->withClaim('first_name', $user->getFirstName())
			->withClaim('last_name', $user->getLastName())
			->withClaim('username', $user->getUsername())
			->withClaim('roles', ['ROLE_USER'])
			->expiresAt($now->modify('+24 hours'))
			->getToken($this->getConfiguration()->signer(), $this->getConfiguration()->signingKey());
	}

	/**
	 * Validates the token against all available constraints.
	 *
	 * @param string $rawToken The JWT token as a string to be parsed and validated.
	 *
	 * @return bool {@code true} if token is valid (passes all constraints), {@code false} otherwise.
	 */
	public function validateToken(string $rawToken): bool {
		$localConfiguration = $this->getConfiguration();
		try {
			$token = $localConfiguration->parser()->parse($rawToken);
			return $localConfiguration->validator()->validate(
				$token,
				...$localConfiguration->validationConstraints()
			);
		} catch (Token\InvalidTokenStructure) {
			// do nothing
		}
		return false;
	}

	/**
	 * Creates readable JWT token object from provided string
	 *
	 * @param string $rawToken The encoded JWT token
	 *
	 * @return UnencryptedToken The readable form of token
	 */
	public function parseToken(string $rawToken): UnencryptedToken {
		$parser = $this->getConfiguration()->parser();
		return $parser->parse($rawToken);
	}
}
