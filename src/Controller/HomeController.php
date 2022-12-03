<?php

namespace App\Controller;

use App\Manager\SantaManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SantaManager $santaManager): Response
    {
        if (null === $this->getUser()) {
            return $this->redirectToRoute("app_login");
        }

        return $this->render('home/index.html.twig');
    }
}
