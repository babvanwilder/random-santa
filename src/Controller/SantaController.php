<?php

namespace App\Controller;

use App\Entity\Santa;
use App\Form\SantaType;
use App\Manager\ParticipateManager;
use App\Manager\SantaManager;
use DateInterval;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route('/santa', name: 'app_santa')]
#[IsGranted('ROLE_USER')]
class SantaController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])]
    public function index(SantaManager $santaManager): Response
    {
        return $this->render("santa/index.html.twig", [
            'openSantas' => $santaManager->getOpen(),
            'futurSantas' => $santaManager->getFuture(),
            'closeSantas' => $santaManager->getClosed(),
            'archivedSantas' => $this->isGranted('ROLE_ADMIN') ? $santaManager->getArchived("P1Y") : []
        ]);
    }

    #[Route('/create', name: '_create', methods: ['GET', 'POST'])]
    public function create(Request $request, SantaManager $santaManager): Response
    {
        $santa = new Santa();
        $now = (new \DateTimeImmutable())->setTime(8, 0);
        while ($now->format('N') !== "1") {
            $now = $now->add(new DateInterval("P1D"));
        }
        $santa
            ->setDateStart($now)
            ->setDateClose($now->add(new DateInterval("P4DT11H")))
            ->setName("Random Santa du " . $santa->getDateStart()->format("d/m/Y"))
        ;
        $form = $this->createForm(SantaType::class, $santa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $santaManager->create($santa->getName(), $santa->getDateStart(), $santa->getDateClose());

            return $this->redirectToRoute('app_santa');
        }

        return $this->renderForm('santa/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(
        '/{santa}',
        name: '_view',
        requirements: [
            'santa' => '\d+'
        ],
        methods: ['GET']
    )]
    public function view(Santa $santa): Response
    {
        if (null !== $santa->getDateArchived() && !$this->isGranted('ROLE_ADMIN')) {
            throw new NotFoundHttpException();
        }

        return $this->render("santa/view.html.twig", [
            'santa' => $santa
        ]);
    }

    #[Route(
        '/{santa}/edit',
        name: '_edit',
        requirements: ['santa' => '\d+'],
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Santa $santa, Request $request, SantaManager $santaManager): Response
    {
        // On n'édite pas un random santa déjà archivé
        if (null !== $santa->getDateArchived()) {
            throw new NotFoundHttpException();
        }

        $oldName = $santa->getName();

        $form = $this->createForm(SantaType::class, $santa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $santaManager->save($santa);

            return $this->redirectToRoute('app_santa_view', [
                'id' => $santa->getId()
            ]);
        }

        return $this->renderForm('santa/edit.html.twig', [
            'form' => $form,
            'oldName' => $oldName
        ]);
    }

    #[Route(
        '/{santa}',
        name: '_archive',
        requirements: [
            'santa' => '\d+'
        ],
        methods: ['DELETE']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function archive(Santa $santa, SantaManager $santaManager): Response
    {
        if (!$this->isGranted('ROLE_ROOT') || $santa->getOwner() !== $this->getUser()) {
            throw new NotFoundHttpException();
        }

        $santaManager->archive($santa);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route(
        '/{santa}/participate',
        name: '_add_me',
        requirements: [
            'santa' => '\d+'
        ],
        methods: ['POST']
    )]
    public function addMe(
        Santa $santa,
        ParticipateManager $participateManager
    ): Response
    {
        if (null !== $participateManager->getBySantaAndGiver($santa, $this->getUser())) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $participate = $participateManager->create($santa);

        return new Response($participate->getId(), Response::HTTP_CREATED);
    }

    #[Route(
        '/{santa}/participate/{participate}',
        name: '_remove_me',
        requirements: [
            'santa' => '\d+',
            'participate' => '\d+'
        ],
        methods: ['DELETE']
    )]
    public function removeMe(
        Santa $santa,
        SantaManager $santaManager,
        ParticipateManager $participateManager,
        LoggerInterface $logger
    ): Response
    {
        $participate = $participateManager->getBySantaAndGiver($santa, $this->getUser());

        if (null === $participate) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $santa->removeParticipate($participate);
        $logger->info($santa->getParticipates()->count());
        $santaManager->save($santa);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route(
        '{santa}/calculate',
        name: '_calculate',
        requirements: [
            'santa' => '\d+',
            'participate' => '\d+'
        ],
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function calculate(Santa $santa, SantaManager $santaManager): Response
    {
        if (!$this->isGranted('ROLE_ROOT') || $santa->getOwner() !== $this->getUser()) {
            throw new AccessDeniedHttpException("not idt");
        }

        $santaManager->calculate($santa);

        return new Response('', Response::HTTP_CREATED);
    }
}
