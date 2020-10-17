<?php
declare(strict_types=1);

namespace App\Controller;

use App\AppRegistry;
use App\Entity\Log;
use App\Service\ChannelService;
use App\Service\CoubAuthService;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class CoubAuthController
 *
 * @package App\Controller
 */
class CoubAuthController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * CoubAuthController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/login", name="app_login_coub")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function loginCoub(Request $request)
    {
        try {
            # ловим ошибку если есть
            if ('' !== (string)$request->getSession()->get('login_error')) {
                $error = $request->getSession()->get('login_error');
                $request->getSession()->set('login_error', '');
                throw new Exception($error);
            }

            if ($this->getUser()) {
                if ($this->getUser()->getBlocked()) {
                    throw new Exception('Пользователь заблокирован');
                }

                return $this->redirectToRoute('main');
            }

            $reg = (string)$request->query->get('registration');

            if (
                'success' !== $reg
                && 'dev' === $_ENV['APP_ENV']
            ) {
                return $this->redirectToRoute('coub_callback');
            }

            if (
                'success' !== $reg
                && '' !== (string)$_ENV['COUB_KEY']
            ) {
                $url = AppRegistry::REQUEST_AUTHORIZE_APP
                    . '?response_type=code'
                    . '&redirect_uri=' . AppRegistry::REDIRECT_CALLBACK
                    . '&client_id=' . $_ENV['COUB_KEY'];

                return $this->redirect($url);
            }
        } catch (Exception $exception) {
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('app_login_coub');
            $logger->setStatus(false);
            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

            return $this->render(
                'coub_auth/login.html.twig',
                [
                    'registration' => false,
                    'error'        => $exception
                ]
            );
        }

        return $this->render(
            'coub_auth/login.html.twig',
            [
                'registration' => true,
            ]
        );
    }

    /**
     * @Route("/auth/callback", name="coub_callback")
     *
     * @param Request         $request
     * @param UserService     $userService
     * @param ChannelService  $channelClient
     * @param CoubAuthService $coubAuthService
     *
     * @return RedirectResponse
     * @throws GuzzleException
     * @throws Exception
     */
    public function callback(
        Request $request,
        UserService $userService,
        ChannelService $channelClient,
        CoubAuthService $coubAuthService
    )
    {
        $tokenData = [];
        $userInfo = [];
        $userSaved = false;
        $code = (string)$request->query->get('code');

        try {
            if ('' !== $code && 'dev' !== $_ENV['APP_ENV']) {
                $tokenData = $coubAuthService->getUserToken($code);

                if (empty($tokenData['access_token'])) {
                    throw new Exception('Не задан токен');
                }
                $userInfo = $userService->getInfo($tokenData['access_token']);

                if (
                    $_ENV['ACCESS_BY_LIST']
                    && 'true' === $_ENV['ACCESS_BY_LIST']
                ) {
                    $isAcccessGranted = $coubAuthService->checkAccessGranted($userInfo);

                    if (true !== $isAcccessGranted) {
                        throw new Exception('Пользователя нет в списке разрешенных!');
                    }
                }

                $userSaved = $userService->saveUser($tokenData, $userInfo);
            }

            if ('dev' === $_ENV['APP_ENV']) {
                $userSaved = true;
                $tokenData['access_token'] = $_ENV['COUB_TEST_TOKEN'];
            }

            if (!$userSaved) {
                throw new Exception('Ошибка при регистрации пользователя');
            }

            if (isset($userInfo['channels'])) {
                $channelClient->saveUserChannelsList($userInfo);
            }

            $request->getSession()->set(
                Security::LAST_USERNAME,
                $tokenData['access_token']
            );
        } catch (Exception $exception) {
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('coub_callback');
            $logger->setRegCode($code);
            if (!empty($tokenData['access_token'])) {
                $logger->setToken($tokenData['access_token']);
            }
            $logger->setStatus(false);
            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

            $request->getSession()->set(
                'login_error',
                $exception->getMessage()
            );

            return $this->redirectToRoute('app_login_coub');
        }

        return $this->redirectToRoute(
            'app_login_coub',
            [
                'registration' => 'success'
            ]
        );
    }
}
