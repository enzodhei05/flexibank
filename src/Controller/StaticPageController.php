<?php

// src/Controller/StaticPageController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticPageController extends AbstractController
{
    
    #[Route(path: '/conditions', name: 'conditions')]
    public function conditions(): Response
    {
        return $this->render('pages/conditions.html.twig');
    }

    #[Route(path: '/politique', name: 'politique')]

    public function politique(): Response
    {
        return $this->render('pages/politique.html.twig');
    }

    #[Route(path: '/aide', name: 'aide')]

    public function aide(): Response
    {
        return $this->render('pages/aide.html.twig');
    }
    
    #[Route(path: '/cookie', name: 'cookie')]

    public function cookie(): Response
    {
        return $this->render('pages/cookie.html.twig');
    }

    #[Route(path: '/home', name: 'home')]
    public function home(): Response
    {
        return $this->render('pages/home.html.twig');
    }
}
