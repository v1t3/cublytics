<?php
declare(strict_types=1);

namespace App\Controller;

use App\CoubToolsService;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        return new Response($data);
    }

    /**
     * @Route("/api/coub/callback", name="coub_callback")
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function callback(Request $request)
    {
        $code = (string)$request->query->get('code');
        if ('' === $code) {
            throw new Exception(
                'Указан некорректный код'
            );
        }

        $coubTool = new CoubToolsService();
        $data = $coubTool->getUserToken($code);
        $data = json_decode($data, true);

        if (isset($data['access_token'])) {
            $userInfo = $coubTool->getUserInfo($data['access_token']);

            if (empty($userInfo)) {
                throw new Exception(
                    'Данные пользователя остутствуют ' . json_encode($userInfo)
                );
            }

            if (isset($userInfo['id'])) {
                $userAccount = $this->entityManager
                    ->getRepository('App:User')
                    ->findOneByChannelId($userInfo['id']);

                if (!$userAccount) {
                    $user = new User();
                    $user->setToken($data['access_token']);
                    $user->setTokenExpiredAt((int)$data['expires_in']);
                    $user->setRoles(['ROLE_USER']);
                    $user->setChannelId($userInfo['id']);
                    $user->setUsername($userInfo['name']);
                    $user->setCreatedAt($userInfo['created_at']);
                    $user->setUpdatedAt($userInfo['updated_at']);

                    //todo Добавить таблицу для каналов, добавить сохранение каналов юзера

                    // todo Реализовать тестовый контур для аутентификации

                    $this->entityManager->persist($user);
                } else {
                    $userAccount->setToken($data['access_token']);
                    $userAccount->setTokenExpiredAt((int)$data['expires_in']);
                    $userAccount->setUpdatedAt($userInfo['updated_at']);

                    $this->entityManager->persist($userAccount);
                }
            }

            $this->entityManager->flush();

//            return $this->redirectToRoute('spa');
        } elseif (isset($data['error'])) {
            throw new Exception('Error code: ' . $data['error'] . ' description: ' . $data['error_description']);
        }

        return new Response(json_encode($data));
    }
}
