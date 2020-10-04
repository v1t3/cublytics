<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        if (
            $this->getUser()
            && true === $this->getUser()->getConfirmed()
        ) {
            $userRoles = $this->getUser()->getRoles();

            // если админ то редирект на админку, иначе на дашборд
            if ($userRoles && in_array('ROLE_ADMIN', $userRoles)) {
                return $this->redirectToRoute('admin_page');
            }

            return $this->redirectToRoute('spa');
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
     */
    public function logout()
    {
        return $this->redirectToRoute('main');
    }

    //todo Реализовать сценарий revoke (удаления токена)

    /**
     * @Route("/confirm/{code}", name="email_confirmation")
     *
     * @param UserRepository $userRepo
     * @param string         $code
     *
     * @return Response
     * @throws \Exception
     */
    public function confirmEmail(UserRepository $userRepo, string $code)
    {
        /**
         * @var User $user
         */
        $user = $userRepo->findOneBy(['confirmation_code' => $code]);

        if ($user === null) {
            return new Response('404');
        }

        $createdAt = $user->getConfirmationCreatedAt();
        $valid = new \DateTime();
        $valid->modify('-24 hours');

        # меньше == раньше
        if ($createdAt < $valid) {
            $user->setConfirmed(false);
            $this->entityManager->persist($user);

            $this->entityManager->flush();

            return $this->render('security/account_confirm_reject.html.twig', []);
        }

        $user->setConfirmed(true);
        $user->setConfirmationCode('');
        $user->setConfirmationCreatedAt(null);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        return $this->render(
            'security/account_confirm.html.twig',
            [
                'user' => $user,
            ]
        );
    }
}
