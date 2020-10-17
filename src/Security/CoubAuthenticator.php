<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class CoubAuthenticator
 *
 * @package App\Security
 */
class CoubAuthenticator extends AbstractGuardAuthenticator
{
    /**
     *
     */
    public const LOGIN_ROUTE = 'app_login_coub';
    /**
     *
     */
    public const START_ROUTE = 'main';
    /**
     *
     */
    public const USER_ROUTE = 'spa';

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * CoubAuthenticator constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface  $urlGenerator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        if (
            self::LOGIN_ROUTE === $request->attributes->get('_route')
            && 'success' === $request->query->get('registration')
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        return [
            'registration' => $request->query->get('registration'),
            'access_token' => $request->getSession()->get(Security::LAST_USERNAME)
        ];
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return User|object|UserInterface|null
     * @throws Exception
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /**
         * @var $userRepo UserRepository
         */
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['token' => $credentials['access_token']]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Пользователь не найден');
        }

        return $user;
    }

    /**
     * @param mixed         $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if (true === $user->getBlocked()) {
            throw new CustomUserMessageAuthenticationException('Пользователь заблокирован');
        }

        $token = $user->getToken();
        if ($token === $credentials['access_token']) {
            //todo проверка даты токена
            //$token_expired_at = $user->getTokenExpiredAt();

            return true;
        }

        return false;
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(
            'login_error',
            $exception->getMessage()
        );

//        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        return null;
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->urlGenerator->generate(self::USER_ROUTE));
    }

    /**
     * Called when authentication is needed, but it's not sent
     *
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->urlGenerator->generate(self::START_ROUTE));
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
