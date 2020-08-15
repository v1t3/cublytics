<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SpaController extends AbstractController
{
    /**
     * @Route("/dashboard/{page}", name="spa")
     *
     * @param string $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index($page = '')
    {
        return $this->render('spa/index.html.twig', [
            'controller_name' => 'SpaController',
        ]);
    }
}
