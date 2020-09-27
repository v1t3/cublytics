<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SpaController
 *
 * @package App\Controller
 */
class SpaController extends AbstractController
{
    /**
     * @Route("/dashboard/{page}", name="spa")
     *
     * @param string $page
     *
     * @return Response
     */
    public function index($page = '')
    {
        return $this->render('spa/index.html.twig', [
            'controller_name' => 'SpaController',
        ]);
    }
}
