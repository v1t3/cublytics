<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MainController
 *
 * @package App\Controller
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/about", name="about_page")
     */
    public function about()
    {
        return $this->render('main/about.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/admin", name="admin_page")
     */
    public function admin()
    {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//            return new RedirectResponse($this->urlGenerator->generate('app_login_admin'));


        return $this->render('main/admin.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
