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

#[Route('/santa', name: 'app_santa')]
class SantaController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])]
    public function index(SantaManager $santaManager): Response
    {
        return $this->render("santa/index.html.twig", [
            'openSantas' => [],
            'closeSantas' => $this->isGranted('ROLE_ADMIN') ? [] : []
        ]);
    }

    #[Route('/create', name: '_create', methods: ['GET', 'POST'])]
    public function create(Request $request, SantaManager $santaManager): Response
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

        return $this->renderForm('santa/create.html.twig', [
            'form' => $form,
        ]);
    }


}
