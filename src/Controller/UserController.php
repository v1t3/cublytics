<?php

namespace App\Controller;

use App\Entity\Log;
use App\Entity\User;
use App\Service\ChannelService;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 *
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * UserController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route ("api/user/get_data", name="get_user_data")
     *
     * @param ChannelService $channelService
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getData(ChannelService $channelService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var $user User
         */
        $user = $this->getUser();

        try {
            $channels = $channelService->getChannelsList();
        } catch (Exception $exception) {
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('get_user_data');
            if ($user) {
                $logger->setUser((string)$user->getUserId());
            }
            $logger->setStatus(false);
            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

            $result = [
                'result' => 'error',
                'error'  => [
                    'message' => $exception->getMessage(),
                ]
            ];

            $response = new JsonResponse();
            $response->setData($result);

            return $response;
        }

        $response = new JsonResponse();
        $response->setData(
            [
                'result' => 'success',
                'data'   => [
                    'user_id'  => $user->getUserId(),
                    'username' => $user->getUsername(),
                    'email'    => $user->getEmail(),
                    'roles'    => $user->getRoles(),
                    'token'    => $user->getToken(),
                    'channels' => $channels,
                ]
            ]
        );

        return $response;
    }

    /**
     * @Route("/api/user/update_settings", name="update_user_settings")
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateSettings(Request $request, UserService $userService)
    {
        try {
            $data = $userService->updateSettings($request);
        } catch (Exception $exception) {
            $user = $this->getUser();

            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('update_user_settings');
            if ($user) {
                $logger->setUser((string)$user->getUserId());
            }
            $logger->setStatus(false);
            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

            $result = [
                'result' => 'error',
                'error'  => [
                    'message' => $exception->getMessage(),
                ]
            ];

            $response = new JsonResponse();
            $response->setData($result);

            return $response;
        }

        $response = new JsonResponse();
        $response->setData($data);

        return $response;
    }

    /**
     * @Route("/api/user/get_username", name="get_user_username")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getUserName(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        try {
            $field = (string)$request->request->get('field');

            if ('' === $field && '' === (string)$user->getUsername()) {
                throw new Exception('Не задано поле field или имя пользователя');
            }

            $data = [
                'result' => 'success',
                'data'   => [
                    'username' => $user->getUsername()
                ]
            ];
        } catch (Exception $exception) {
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('get_user_username');
            if ($user) {
                $logger->setUser((string)$user->getUserId());
            }
            $logger->setStatus(false);
            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

            $result = [
                'result' => 'error',
                'error'  => [
                    'message' => $exception->getMessage(),
                ]
            ];

            $response = new JsonResponse();
            $response->setData($result);

            return $response;
        }

        $response = new JsonResponse();
        $response->setData($data);

        return $response;
    }

    /**
     * @Route("/api/user/get_settings", name="get_user_settings")
     *
     * @param UserService $userService
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getSettings(UserService $userService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var $user User
         */
        $user = $this->getUser();

        try {
            if (!$user) {
                throw new Exception('Пользователь не найден');
            }

            $data = $userService->getSettings($user);

            $result = [
                'result'  => 'success',
                'message' => '',
                'data'    => $data
            ];
        } catch (Exception $exception) {
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('get_user_username');
            if ($user) {
                $logger->setUser((string)$user->getUserId());
            }
            $logger->setStatus(false);
            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
            $this->entityManager->persist($logger);
            $this->entityManager->flush();

            $result = [
                'result' => 'error',
                'error'  => [
                    'message' => $exception->getMessage(),
                ]
            ];

            $response = new JsonResponse();
            $response->setData($result);

            return $response;
        }

        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }
}
