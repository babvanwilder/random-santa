<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Manager\UserManager;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Throwable;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly VerifyEmailHelperInterface $verifyEmailHelper
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserManager $userManager,
        MailerInterface $mailer,
        LoggerInterface $logger
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $userManager->save($user);
            // generate a signed url and email it to the user
            $signature = $this->verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                [
                    'id' => $user->getId()
                ]
            );

            $logger->info("id: {$user->getId()} | email: {$user->getEmail()}");

            $email = (new TemplatedEmail())
                ->from(new Address('ramdomsanta@codes-rousseau.fr', 'Random Santa'))
                ->to(new Address($user->getEmail(), $user->getFirstname() . " " .$user->getLastname()))
                ->subject('Confirmation de votre email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'signedUrl' => $signature
                ]);
            ;

            $mailer->send($email);

            return $this->redirectToRoute('app_succeeded_register');
        }

        return $this->renderForm('registration/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/register/succeeded', name: 'app_succeeded_register')]
    public function succeeded(): Response
    {
        return $this->render('registration/succeeded.html.twig');
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserManager $userManager, LoggerInterface $logger): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_home');
        }

        $user = $userManager->getOne($id);

        if (null === $user) {
            return $this->redirectToRoute('app_home');
        }
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $logger->info(get_class($this->verifyEmailHelper));
            $this->verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
            $userManager->save($user->setIsVerified(true));
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre compte a bien été vérifié.');

        return $this->redirectToRoute('app_login');
    }
}
