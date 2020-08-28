<?php
declare(strict_types=1);

namespace App\Controller;

use App\ChannelService;
use App\CoubAuthService;
use App\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CoubAuthController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/coub/callback", name="coub_callback")
     *
     * @param Request         $request
     * @param UserService     $userService
     * @param ChannelService  $channelClient
     * @param CoubAuthService $coubAuthService
     *
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
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

        if ($userSaved && isset($userInfo['channels'])) {
            $channelSaved = $channelClient->saveUserChannels($userInfo);
        } else {
            throw new Exception(
                'Ошибка при регистрации пользователя'
            );
        }

        $result = [
            'registration' => 'success',
            'userSaved'    => $userSaved,
            'channelSaved' => $channelSaved,
            'access_token' => $tokenData['access_token']
        ];

        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

}
