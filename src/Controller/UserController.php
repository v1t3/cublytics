<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/api/user/update_settings", name="update_user_settings")
     *
     * @param Request     $request
     * @param UserService $userService
     *
     * @return JsonResponse
     */
    public function updateSettings(Request $request, UserService $userService)
    {
        $data = $userService->updateSettings($request);

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
     */
    public function getUserName(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $field = (string)$request->request->get('field');
        $user = $this->getUser();

        if ('' !== $field && '' !== $user->getUsername()) {
            $data = [
                'result'  => 'success',
                'message' => $user->getUsername()
            ];
        } else {
            $data = [
                'result'  => 'error',
                'message' => ''
            ];
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
     */
    public function getSettings(UserService $userService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /**
         * @var $user User
         */
        $user = $this->getUser();

        if ($user) {
            $data = $userService->getSettings($user);

            $result = [
                'result'  => 'success',
                'message' => '',
                'data'    => $data
            ];
        } else {
            $result = [
                'result'  => 'error',
                'message' => 'Пользователь не найден'
            ];
        }

        $response = new JsonResponse();
        $response->setData($result);

        return $response;
    }
}
