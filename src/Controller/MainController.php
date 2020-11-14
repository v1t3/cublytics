<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    public function index(): Response
    {
        return $this->render(
            'main/index.html.twig',
            [
                'controller_name' => 'MainController',
            ]
        );
    }

    /**
     * @Route("/about", name="about_page")
     */
    public function about(): Response
    {
        return $this->render(
            'main/about.html.twig',
            [
                'controller_name' => 'MainController',
            ]
        );
    }

    /**
     * @Route("/terms", name="terms_page")
     */
    public function terms(): Response
    {
        return $this->render(
            'main/terms.html.twig',
            [
                'controller_name' => 'MainController',
            ]
        );
    }

    /**
     * @Route("/agreement", name="agreement_page")
     */
    public function agreement(): Response
    {
        return $this->render(
            'main/agreement.html.twig',
            [
                'controller_name' => 'MainController',
            ]
        );
    }

    /**
     * @Route("/admin", name="admin_page")
     */
    public function admin(): Response
    {
        return $this->render(
            'main/admin.html.twig',
            [
                'controller_name' => 'MainController',
            ]
        );
    }
}
