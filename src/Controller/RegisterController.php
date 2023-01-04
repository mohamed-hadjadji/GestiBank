<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class RegisterController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager=$entityManager;
    }

    #[Route('/inscription', name: 'app_register')]
    public function new(Request $request, UserPasswordHasherInterface $encoder, MailerService $mailer, MailerInterface $mailerinterface): Response
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            if(!$search_email){
            $pwd = $user->getPassword();
            $password = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setCreatedAt(new \DateTimeImmutable());
            //dd($user);
            $user->setRoles(array('ROLE_USER'));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('notice', 'vous etes bien inscrit(e), vous pouvez dès à présent vous connecter à votre compte.');
            $mailMessage = 'Bonjour votre inscription a bien été envoyée, vous pouvez se connecter avec votre email :' .$user->getUserIdentifier().' '.' et votre mot de passe :' .$pwd;
            $email = $user->getUserIdentifier();
            $mailer->sendEmail($mailerinterface, $mailMessage, $email);
            return $this->redirectToRoute('app_register');
            }else{
                $notification = "L'email que vous avez renseigné existe déja.";
            }
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification'=> $notification
        ]);
    }
}
