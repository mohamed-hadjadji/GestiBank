<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\AgentType;
use App\Repository\UserRepository;
use App\Services\MailerService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/agents')]
class AgentController extends AbstractController
{
    #[Route('/liste-agents', name: 'app_agent_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, ManagerRegistry $doctrine, EntityManagerInterface $em): Response
    {
        $repository = $doctrine->getRepository(User::class);
        $users = $repository->findByRole("ROLE_AGENT");
        return $this->render('agent/index.html.twig', [
            'users' => $users]);
    }

    #[Route('/new', name: 'app_agent_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agent/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/add', name: 'app_agent_add', methods: ['GET', 'POST'])]
    public function add(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $encoder,  MailerService $mailer, MailerInterface $mailerinterface): Response
    {
        $entityManager = $doctrine->getManager();
        $data = $request->request->all();
        $user = new User();

            $passBeforeHashage = $data['password'];
            $password = $encoder->hashPassword($user, $data['password']);
            $user->setPassword($password);
            $user->setMatricule($data['matricule']);
            $user->setFirstname($data['firstname']);
            $user->setLastname($data['lastname']);
            $user->setEmail($data['email']);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setRoles(array('ROLE_AGENT'));
            $entityManager->persist($user);
            $entityManager->flush();
            $mailMessage = $user->getUserIdentifier(). 'a été bien ajoutée avec succes tant que agent et votre Matricule est:'.''.$user->getMatricule().' et votre mot de passe est : '.$passBeforeHashage;
            $email = $user->getEmail();
            $mailer->sendEmail($mailerinterface, $mailMessage, $email);
            $this->addFlash(type: 'success', message: "Agent ajouté avec succès");
            return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/{id}', name: 'app_agent_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('agent/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_agent_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(AgentType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);
            $this->addFlash(type: 'success', message: "Agent édité avec succès");
            return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agent/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_agent_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
            $this->addFlash(type: 'success', message: "Agent supprimé avec succès");
        }

        return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
    }
}
