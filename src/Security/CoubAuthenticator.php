<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
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
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

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
//            throw new \Exception(json_encode($result));

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
        $credentials = [
            'registration' => $request->query->get('registration'),
            'access_token' => $request->getSession()->get(Security::LAST_USERNAME)
        ];

        return $credentials;
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return User|object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!isset($credentials['access_token'])) {
            return null;
        }

        /**
         * @var $userRepo UserRepository
         */
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['token' => $credentials['access_token']]);

        if (!$user) {
            return null;
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
     * @return JsonResponse|Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->urlGenerator->generate('spa'));
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
        return new RedirectResponse($this->urlGenerator->generate('main'));
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
