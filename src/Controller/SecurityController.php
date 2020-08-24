<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     *
     */
    public const REQUEST_AUTHORIZE_APP = 'http://coub.com/oauth/authorize';

    /**
     *
     */
    public const REDIRECT_CALLBACK = 'https://a8a2734f0ca4.ngrok.io/api/coub/callback';

    /**
     *
     */
    public const REQUEST_REVOKE_APP = 'http://coub.com/oauth/revoke';

    /**
     * @Route("/login", name="app_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //todo Настроить общую систему авторизации
        if ($this->getUser()) {
            return $this->redirectToRoute('main');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/login-coub", name="app_login_coub")
     */
    public function loginCoub()
    {
        if ((string)$_ENV['COUB_KEY'] !== '') {
            $url = self::REQUEST_AUTHORIZE_APP
                . '?response_type=code'
                . '&redirect_uri=' . self::REDIRECT_CALLBACK
                . '&client_id=' . $_ENV['COUB_KEY'];

            return $this->redirect($url);
        }

        return new Response('env empty');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
//        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

        return $this->redirectToRoute('main');
    }

    //todo Реализовать сценарий revoke (удаления токена)
}
