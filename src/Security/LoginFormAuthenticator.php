<?php
namespace App\Security;

use App\Repository\UserRepository;
use http\Client\Curl\User;
use http\Env\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    public const LOGIN_ROUTE = 'app_login';

    private $urlGenerator;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            // Email doesn't exist — let Symfony handle wrong credentials
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }

        if (!$user->isIsVerified()) {
            // Email not verified — block login attempt
            throw new CustomUserMessageAuthenticationException('Please verify your email before logging in.');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?\Symfony\Component\HttpFoundation\Response
    {
        $user = $token->getUser();

        if ($user instanceof UserInterface) {
            $roles = $user->getRoles();

            if (in_array('ROLE_ADMIN', $roles, true)) {
                return new RedirectResponse($this->urlGenerator->generate('app_book_index'));
            }

            if (in_array('ROLE_CUSTOMER', $roles, true)) {
                return new RedirectResponse($this->urlGenerator->generate('available_books'));
            }
        }

        // fallback: redirect to homepage
        return new RedirectResponse($this->urlGenerator->generate('app_homepage'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
