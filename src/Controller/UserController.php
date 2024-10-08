<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\ConfirmationRequest;
use App\Entity\Log;
use App\Entity\User;
use App\Repository\ConfirmationRequestRepository;
use App\Repository\UserRepository;
use App\Service\ChannelService;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function getData(ChannelService $channelService): JsonResponse
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

        /**
         * @var $confRepo ConfirmationRequestRepository
         */
        $confRepo = $this->entityManager->getRepository(ConfirmationRequest::class);
        $confirm = $confRepo->findOneBy(['owner_id' => $user->getId()]);

        $response = new JsonResponse();
        $response->setData(
            [
                'result' => 'success',
                'data'   => [
                    'user_id'      => $user->getUserId(),
                    'username'     => $user->getUsername(),
                    'email'        => $user->getEmail(),
                    'roles'        => $user->getRoles(),
                    'confirmed'    => $confirm ? $confirm->getConfirmed() : false,
                    'password_set' => ('' !== $user->getPassword()),
                    'channels'     => $channels,
                ]
            ]
        );

        return $response;
    }

    /**
     * @Route("/api/user/update_settings", name="update_user_settings")
     *
     * @param Request       $request
     * @param UserService   $userService
     * @param Mailer        $mailer
     * @param CodeGenerator $codeGenerator
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateSettings(
        Request $request,
        UserService $userService,
        Mailer $mailer,
        CodeGenerator $codeGenerator
    ): JsonResponse {
        $result = [
            'result' => 'error',
            'error'  => [
                'message' => 'Временно не доступно',
            ]
        ];

        $response = new JsonResponse();
        $response->setData($result);

        return $response;

//        try {
//            $data = $userService->updateSettings($request, $mailer, $codeGenerator);
//        } catch (Exception $exception) {
//            $user = $this->getUser();
//
//            $this->entityManager->clear();
//            $logger = new Log();
//            $logger->setDate(new DateTime('now'));
//            $logger->setType('update_user_settings');
//            if ($user) {
//                $logger->setUser((string)$user->getUserId());
//            }
//            $logger->setStatus(false);
//            $logger->setError('Код ' . $exception->getCode() . ' - ' . $exception->getMessage());
//            $this->entityManager->persist($logger);
//            $this->entityManager->flush();
//
//            $result = [
//                'result' => 'error',
//                'error'  => [
//                    'message' => $exception->getMessage(),
//                ]
//            ];
//
//            $response = new JsonResponse();
//            $response->setData($result);
//
//            return $response;
//        }
//
//        $response = new JsonResponse();
//        $response->setData(
//            [
//                'result'  => 'success',
//                'message' => 'Обновлено!' . $data,
//            ]
//        );
//
//        return $response;
    }

    /**
     * @Route("/api/user/get_username", name="get_user_username")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getUserName(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        try {
            $field = (string)$request->request->get('field');

            if (!$user) {
                throw new RuntimeException('Пользователь отсутсвует');
            }
            if ('' === $field && '' === (string)$user->getUsername()) {
                throw new RuntimeException('Не задано поле field или имя пользователя');
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
    public function getSettings(UserService $userService): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var $user User
         */
        $user = $this->getUser();

        try {
            if (!$user) {
                throw new RuntimeException('Пользователь не найден');
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
            $logger->setType('get_user_settings');
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

    /**
     * //todo сценарий revoke
     *
     * @Route("/api/user/delete_account", name="delete_account")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return JsonResponse|RedirectResponse
     * @throws \Exception
     */
    public function deleteAccount(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /**
         * @var $userRepo UserRepository
         * @var $user     User
         */
        $userRepo = $this->entityManager->getRepository(User::class);
        if ($this->getUser()) {
            $user = $userRepo->find($this->getUser()->getId());
        }

        try {
            if (!$user) {
                throw new RuntimeException('Пользователь отсутствует');
            }

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();

            $result = [
                'result'  => 'success',
                'message' => '',
                'data'    => ''
            ];
        } catch (Exception $exception) {
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('get_user_settings');
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
