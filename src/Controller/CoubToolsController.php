<?php
declare(strict_types=1);

namespace App\Controller;

use App\ChannelService;
use App\CoubToolsService;
use App\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CoubToolsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/coub/getdata", name="getData")
     *
     * @param Request $request
     *
     * @return string|Response
     */
    public static function getData(Request $request)
    {
        $data = '';

        if ($request->isMethod('post')) {
            $params = $request->request->get('params');

            $params = json_decode(html_entity_decode($params), true);

            if ((string)$params['url'] === '') {
                return '';
            }

            $coub = new CoubToolsService();

            if ($coub) {
                if ((string)$params['type'] === 'userdata') {
                    $coubContent = $coub->getUserData($params['url']);
                } elseif ((string)$params['type'] === 'coubdata') {
                    $coubContent = $coub->getCoubData($params['url']);
                } elseif ((string)$params['type'] === 'performance') {
                    $coubContent = $coub->getChannelPerf($params['url']);
                } else {
                    $coubContent = '';
                }

                if (is_array($coubContent) && count($coubContent) > 0) {
                    $data = json_encode($coubContent);
                } elseif ('' !== (string)$coubContent) {
                    $data = $coubContent;
                }
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/coub/callback", name="coub_callback")
     *
     * @param Request        $request
     * @param UserService    $userService
     * @param ChannelService $channelClient
     *
     * @return Response
     * @throws Exception
     */
    public function callback(
        Request $request,
        UserService $userService,
        ChannelService $channelClient
    )
    {
        //ответ от сервиса регистраниции коуба
        $code = (string)$request->query->get('code');
        if ('' === $code) {
            throw new Exception(
                'Указан некорректный код'
            );
        }

        $coubTool = new CoubToolsService();
        $tokenData = $coubTool->getUserToken($code);
        $tokenData = json_decode($tokenData, true);

        if (isset($tokenData['access_token'])) {
            $userInfo = $coubTool->getUserInfo($tokenData['access_token']);

            if (empty($userInfo)) {
                throw new Exception(
                    'Данные пользователя остутствуют '
//                    . json_encode($userInfo)
                );
            }

            $userSaved = $userService->saveUser($tokenData, $userInfo);

            if ($userSaved) {
                if (isset($userInfo['channels'])) {
                    $channelSaved = $channelClient->saveUserChannels($userInfo);
                }
            } else {
                throw new Exception(
                    'Ошибка при регистрации пользователя'
                );
            }

            return $this->redirectToRoute(
                'app_login',
                [
                    'registration' => 'success',
                    'access_token' => $tokenData['access_token']
                ]
            );
        } elseif (isset($tokenData['error'])) {
            throw new Exception(
                'Error code: ' . $tokenData['error']
                . ' description: ' . $tokenData['error_description']
            );
        }
    }
}
