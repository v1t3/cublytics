<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
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
     * @Route("/test/coub/callback", name="test_user_info")
     */
    public function getTestUserInfo(Request $request)
    {
        $token = (string)$request->query->get('access_token');

        if ($token !== $_ENV['TEST_TOKEN']) {
            throw new \Exception(
                'Указан некорректный token' . $token . '/' . $_ENV['TEST_TOKEN']
            );
        }
        $testTokenData = [
            'access_token' => $_ENV['TEST_TOKEN'],
            'expires_in' => 0,
            'created_at' => 0,
        ];

        $testUserInfo = $_ENV['TEST_DATA'];
        $testUserInfo = json_decode($testUserInfo, true);

        $res = $this->saveTestUserInfo($testTokenData, $testUserInfo);

        $response = new Response();
        $response->setContent(json_encode([$res, $testUserInfo]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function saveTestUserInfo($tokenData, $userInfo)
    {
        if (isset($userInfo['id'])) {
            $entityManager = $this->getDoctrine()->getManager();

            $userAccount = $entityManager
                ->getRepository('App:User')
                ->findOneByUserId($userInfo['id']);

            if (!$userAccount) {
                $user = new User();
                $user->setToken($tokenData['access_token']);
                $user->setTokenExpiredAt((int)$tokenData['expires_in'] + (int)$tokenData['created_at']);
                $user->setRoles(['ROLE_USER']);
                $user->setUserId($userInfo['id']);
                $user->setUsername($userInfo['name']);
                $user->setCreatedAt($userInfo['created_at']);
                $user->setUpdatedAt($userInfo['updated_at']);

                //todo Добавить таблицу для каналов, добавить сохранение каналов юзера

                $entityManager->persist($user);
            } else {
                $userAccount->setToken($tokenData['access_token']);
                $userAccount->setTokenExpiredAt((int)$tokenData['expires_in']);
                $userAccount->setUpdatedAt($userInfo['updated_at']);

                $entityManager->persist($userAccount);
            }

            $entityManager->flush();

            return true;
        }

        return false;
    }
}
