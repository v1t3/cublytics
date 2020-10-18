<?php

namespace App\Controller;

use App\AppRegistry;
use App\Entity\Coub;
use App\Entity\User;
use App\Service\ChannelService;
use App\Service\CoubAuthService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

//    /**
//     * @Route("/test/oauth/authorize", name="test_authorize")
//     */
//    public function getTestAuthorize(Request $request)
//    {
//        $response_type = (string)$request->query->get('response_type');
//        $redirect_uri = (string)$request->query->get('redirect_uri');
//        $client_id = (string)$request->query->get('client_id');
//
//        if (
//            'code' !== $response_type
//            || '' === $redirect_uri
//            || $client_id !== $_ENV['COUB_KEY']
//        ) {
//            throw new \Exception(
//                'Указаны некорректные response_type, redirect_uri или client_id'
//            );
//        }
//
//        return $this->redirect($redirect_uri . '?code=' . $_ENV['TEST_CODE']);
//    }

//    /**
//     * @Route("/test/oauth/token", name="test_user_token")
//     */
//    public function getTestUserToken(Request $request)
//    {
//        $grant_type = (string)$request->request->get('grant_type');
//        $redirect_uri = (string)$request->request->get('redirect_uri');
//        $client_id = (string)$request->request->get('client_id');
//        $client_secret = (string)$request->request->get('client_secret');
//        $code = (string)$request->request->get('code');
//
//        if (
//            'authorization_code' !== $grant_type
//            || '' === $redirect_uri
//            || $client_id !== $_ENV['COUB_KEY']
//            || $client_secret !== $_ENV['COUB_SECRET']
//            || $code !== $_ENV['TEST_CODE']
//        ) {
//            throw new \Exception(
//                'Указаны некорректные grant_type, redirect_uri, client_id, client_secret, code'
//            );
//        }
//
//        return $this->redirect($redirect_uri . '?token=' . $_ENV['TEST_TOKEN']);
//    }

    /**
     * @Route("/test/auth/callback", name="test_user_info")
     *
     * @param Request         $request
     * @param UserService     $userService
     * @param ChannelService  $channelService
     * @param CoubAuthService $coubAuthService
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getTestUserInfo(
        Request $request,
        UserService $userService,
        ChannelService $channelService,
        CoubAuthService $coubAuthService
    )
    {
        $token = (string)$request->query->get('access_token');

        if ($token !== $_ENV['COUB_TEST_TOKEN']) {
            $response = new JsonResponse();
            $response->setContent('Не указан или некорректный тестовый токен');

            return $response;
        }
        $testTokenData = [
            'access_token' => $_ENV['COUB_TEST_TOKEN'],
            'expires_in'   => 0,
            'created_at'   => 0,
        ];

        $userInfo = $_ENV['TEST_DATA'];
        $userInfo = json_decode($userInfo, true);

        if ('true' === $_ENV['ACCESS_BY_LIST']) {
            $isAcccessGranted = $coubAuthService->checkAccessGranted($userInfo);

            if (true !== $isAcccessGranted) {
                $response = new JsonResponse();
                $response->setData(
                    [
                        'Пользователя нет в списке разрешенных!',
                        $isAcccessGranted,
                        'ACCESS_BY_LIST' => $_ENV['ACCESS_BY_LIST']
                    ]
                );

                return $response;
            }
        }

        $userSaved = $userService->saveUser($testTokenData, $userInfo);

        if ($userSaved) {
            if (isset($userInfo['channels'])) {
                $channelSaved = $channelService->saveUserChannelsList($userInfo);
            }
        }

        $response = new JsonResponse();
        $response->setData(
            [
                'Пользователь сохранён' => $userSaved,
                'Каналы сохранены'      => $channelSaved
            ]
        );

        return $response;
    }

    public function saveTestUserInfo($tokenData, $userInfo)
    {
        if (isset($userInfo['id'])) {
            /**
             * @var $userAccount User
             */
            $userAccount = $this->entityManager
                ->getRepository('App:User')
                ->findOneByUserId($userInfo['id']);

            if (!$userAccount) {
                $userAccount = new User();
                $userAccount->setToken($tokenData['access_token']);
                $userAccount->setTokenExpiredAt(
                    (int)$tokenData['expires_in'] + (int)$tokenData['created_at']
                );
                $userAccount->setRoles(['ROLE_USER']);
                $userAccount->setUserId($userInfo['id']);
                $userAccount->setUsername($userInfo['name']);
                $userAccount->setCreatedAt($userInfo['created_at']);
                $userAccount->setUpdatedAt($userInfo['updated_at']);

                $this->entityManager->persist($userAccount);
            } else {
                $userAccount->setToken($tokenData['access_token']);
                $userAccount->setRoles(['ROLE_USER']);
                $userAccount->setTokenExpiredAt((int)$tokenData['expires_in']);
                $userAccount->setUpdatedAt($userInfo['updated_at']);

                $this->entityManager->persist($userAccount);
            }

            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @Route("/savecoub", name="savecoub")
     */
    public function saveTestCoub()
    {
        $channelId = 1111;
        $coubRepo = $this->entityManager->getRepository(Coub::class);

        /**
         * @var $coubItem Coub
         */
        $coubItem = $coubRepo->findOneBy(['channel_id' => $channelId]);

        if ($coubItem) {
            $coubItem->setChannelId($channelId);
            $coubItem->setTitle('title');
//            $coubItem->setUpdatedAt($coub['updated_at']);
            //$coubItem->setDeletedAt();
            //todo Реализовать проверку существования коуба при выполнении задания cron

            //добавим коуб к сохранению
            $this->entityManager->persist($coubItem);
        } else {
            $coubItem = new Coub();
            $coubItem->setCoubId(111);
            $coubItem->setChannelId($channelId);
            $coubItem->setPermalink('permalink');
            $coubItem->setTitle('title');
//            $coubItem->setCreatedAt($coub['created_at']);
//            $coubItem->setUpdatedAt($coub['updated_at']);

            //добавим коуб к сохранению
            $this->entityManager->persist($coubItem);
        }

        $this->entityManager->flush();

        $response = new JsonResponse();
        $response->setData(
            [
                'result'  => 'success',
                'message' => [
                    'id'     => $coubItem->getCoubId(),
                    'create' => $coubItem->getDateCreate(),
                    'update' => $coubItem->getDateUpdate(),
                ]
            ]
        );

        return $response;
    }

    /**
     * @Route("/test/get_headers", name="get_headers")
     */
    public function getHeaders(Request $request)
    {
        $result = [];
        $channel = false;
        $page = $request->query->get('page');

        if ($page) {
            $result = get_headers('https://coub.com/' . $page);

            if ($result && strpos($result[0], '200')) {
                $channel = true;
            }
        }

        $response = new JsonResponse();
        $response->setData([$result, $channel]);

        return $response;
    }

    /**
     * @Route("/test/get_original_coubs", name="test_get_original_coubs")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Service\ChannelService               $channelService
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function getTestOriginalCoubs(Request $request, ChannelService $channelService)
    {
        $result = [];

        $channelName = $request->query->get('channel_name');

        if ('' === $channelName) {
            throw new Exception('Не корректно или не заполнено поле channel_name');
        }

        $urlTale = '&per_page=' . AppRegistry::TIMELINE_PER_PAGE . '&order_by=' . AppRegistry::TIMELINE_ORDER_BY;
        //получим основные данные канала
        $data = $channelService->getInfo(AppRegistry::API_COUB_TIMELINE_LINK . $channelName . '?page=1' . $urlTale);

        if ('' === (string)$data) {
            throw new Exception('Возвращён пустой ответ');
        }
        // проверим, что вернулся не html
        if (false !== strpos((string)$data, '<!DOCTYPE html>')) {
            throw new Exception('Некорректный ответ от сервиса');
        }

        $decodeData = json_decode(html_entity_decode($data), true);

        if (
            !is_array($decodeData)
            || !array_key_exists('total_pages', $decodeData)
        ) {
            throw new Exception('Ошибка при получении данных data: ' . json_encode($data));
        }

        if (array_key_exists('total_pages', $decodeData)) {
            if (1 < (int)$decodeData['total_pages']) {
                $urls = [];
                $allCoubs = [];
                $allCoubsTemp = [];
                # сохраним уже полученную 1ю страницу
                $encodeData[] = $data;

                # получим грязный список страниц всех коубов
                for ($i = 2; $i <= $decodeData['total_pages']; $i++) {
                    $urls[] = AppRegistry::API_COUB_TIMELINE_LINK . $channelName . '?page=' . $i . $urlTale;
                }

                $others = $channelService->getInfoByUrls($urls);

                if (is_array($others) && !empty($others)) {
                    $encodeData = array_merge($encodeData, $others);
                }

                # получаем коубы постранично и объединяем в общий массив
                foreach ($encodeData as $item) {
                    $decodeTemp = json_decode(html_entity_decode($item), true);
                    if (is_array($decodeTemp['coubs'])) {
                        $allCoubs[] = $decodeTemp['coubs'];
                    }
                }
                # сольём массив
                $result = array_merge([], ...$allCoubs);
                # уберём дубликаты коубов
                $result = $channelService->arrayUniqueKey($result, 'id');
            } elseif (1 === $decodeData['total_pages']) {
                $result = $decodeData['coubs'];
            }
        }

        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }

}
