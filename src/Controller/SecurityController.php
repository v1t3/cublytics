<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\ConfirmationRequest;
use App\Entity\Log;
use App\Repository\ConfirmationRequestRepository;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * SecurityController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/login", name="app_login_admin")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            /**
             * @var $confRepo ConfirmationRequestRepository
             */
            $confRepo = $this->entityManager->getRepository(ConfirmationRequest::class);
            $confirm = $confRepo->findOneBy(['owner_id' => $this->getUser()->getId()]);

            if (
                $confirm
                && true === $confirm->getConfirmed()
            ) {
                $userRoles = $this->getUser()->getRoles();

                // если админ то редирект на админку, иначе на дашборд
                if ($userRoles && in_array('ROLE_ADMIN', $userRoles)) {
                    return $this->redirectToRoute('admin_page');
                }

                return $this->redirectToRoute('spa');
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render(
            'security/login.html.twig',
            [
                'error' => $error
            ]
        );
    }

    /**
     * @Route("/logout", name="app_logout")
     *
     * @return RedirectResponse
     */
    public function logout()
    {
        return $this->redirectToRoute('main');
    }

    //todo Реализовать сценарий revoke (удаления токена)

    /**
     * @Route("/confirm/{code}", name="email_confirmation")
     *
     * @param string $code
     *
     * @return Response
     * @throws Exception
     */
    public function confirmEmail(string $code = '')
    {
        if ('' === (string)$code) {
            return $this->redirectToRoute('main');
        }

        /**
         * @var $confirmRepo  ConfirmationRequestRepository
         * @var $confirmation ConfirmationRequest
         */
        $confirmRepo = $this->entityManager->getRepository(ConfirmationRequest::class);
        $confirmation = $confirmRepo->findOneBy(['code' => $code]);

        if (!$confirmation) {
            return new Response('404');
        }

        $expiredAt = $confirmation->getExpiresAt();

        # меньше == раньше
        if ($expiredAt < new DateTime()) {
            $confirmation->setConfirmed(false);
            $this->entityManager->persist($confirmation);

            $this->entityManager->flush();

            return $this->render('security/account_confirm_reject.html.twig', []);
        }

        $confirmation->setConfirmed(true);
        $confirmation->setCode('');
        $confirmation->setExpiresAt(null);
        $this->entityManager->persist($confirmation);

        $this->entityManager->flush();

        return $this->render('security/account_confirm.html.twig', []);
    }

    /**
     * @Route("/resend_confirmation", name="resend_confirmation")
     *
     * @param UserService   $userService
     * @param Mailer        $mailer
     * @param CodeGenerator $codeGenerator
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function resendConfirmation(
        UserService $userService,
        Mailer $mailer,
        CodeGenerator $codeGenerator
    )
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $data = $userService->resendConfirmation($mailer, $codeGenerator);
        } catch (Exception $exception) {
            $user = $this->getUser();
            $this->entityManager->clear();
            $logger = new Log();
            $logger->setDate(new DateTime('now'));
            $logger->setType('resend_confirmation');
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
                'result'  => 'success',
                'message' => $data,
            ]
        );

        return $response;
    }
}
