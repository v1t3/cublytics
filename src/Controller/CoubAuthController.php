<?php
declare(strict_types=1);


namespace App\Controller;


use App\AppRegistry;
use App\ChannelService;
use App\CoubAuthService;
use App\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


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
     * @param Request             $request
     *
     * @return RedirectResponse|Response
     */
    public function loginCoub(Request $request)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('main');
        }

        $reg = (string)$request->query->get('registration');
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

        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/api/coub/callback", name="coub_callback")
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
        $code = (string)$request->query->get('code');

        if ('' !== $code) {
            $tokenData = $coubAuthService->getUserToken($code);

            if (isset($tokenData['access_token'])) {
                $userInfo = $userService->getInfo($tokenData['access_token']);
            }
        }

        $userSaved = $userService->saveUser($tokenData, $userInfo);

        if (!$userSaved) {
            throw new Exception(
                'Ошибка при регистрации пользователя'
            );
        }

        if (isset($userInfo['channels'])) {
            $channelClient->saveUserChannels($userInfo);
        }

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $tokenData['access_token']
        );

        return $this->redirectToRoute(
            'app_login_coub',
            [
                'registration' => 'success'
            ]
        );
    }

}
