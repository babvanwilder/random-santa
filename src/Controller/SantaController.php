<?php

namespace App\Controller;

use App\Entity\Santa;
use App\Form\SantaType;
use App\Manager\SantaManager;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SantaController extends AbstractController
{
    #[Route('/santa', name: 'app_santa_create', methods: ['GET', 'POST'])]
    public function index(Request $request, SantaManager $santaManager): Response
    {
        $santa = new Santa();
        $santa
            ->setYear((new DateTime())->format("Y"))
            ->setName("Random Santa de " . $santa->getYear());
        $form = $this->createForm(SantaType::class, $santa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $santaManager->create($santa->getName(), $santa->getYear());

            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm('santa/index.html.twig', [
            'form' => $form,
        ]);
    }
}
