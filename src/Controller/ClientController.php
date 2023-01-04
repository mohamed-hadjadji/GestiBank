<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\User;
use App\Form\ClientType;
use App\Form\UserType;
use App\Repository\CompteRepository;
use App\Repository\DemandeRepository;
use App\Repository\UserRepository;
use App\Services\MailerService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{

    // Affichage des comptes des clients
    #[Route('/client/compte/{id}', name: 'app_client_compte', methods: ['GET'])]
    public function compte(CompteRepository $compteRepository, $id): Response
    {
        $comptes = $compteRepository->findBy(array('user' => $id));
        //dd($comptes);
        return $this->render('client/compte.html.twig', [
            'comptes' => $comptes
        ]);
    }

    //Effectuer un virement
    #[Route('/client/compte/detil/{id}', name: 'app_detail_compte')]
    public function vir(CompteRepository $compteRepository, $id): Response
    {
        $accounts = $compteRepository->findBy(array('id' => $id));
        //dd($accounts);
        return $this->render('client/detail.html.twig', [
            'details' => $accounts
        ]);
    }


    //afficher mes infos
    #[Route('/client/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(DemandeRepository $demandeRepository, $id): Response
    {
        //dd($id);
        $clients = $demandeRepository->findOneBy(array('idUser' => $id));
        //dd($clients);
        return $this->render('client/show.html.twig', [
            'client' => $clients
        ]);
    }

    // modifier mon profil

    #[Route('/client/{id}/modifier', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Demande $client, DemandeRepository $demandeRepository, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(User::class);
        $agents = $repository->findByRole("ROLE_AGENT");
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$agent = $request->get('agent');
            //$demande->setAgent($agent);
            $demandeRepository->save($client, true);

            return $this->redirectToRoute('app_client_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
            'agents' => $agents

        ]);
    }
    //Changer mot de passe de client
    #[Route('/client/edit-password/{id}', name: 'app_client_pass', methods: ['GET', 'POST'])]
    public function password(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $encoder, MailerService $mailer, MailerInterface $mailerinterface): Response
    {
        $notification = null;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData();
            if($encoder->isPasswordValid($user, $old_pwd)){
                $new_pwd = $form->get('new_password')->getData();
                $password = $encoder->hashPassword($user, $new_pwd);
                $user->setPassword($password);
                //$user->getEmail();
                $mailMessage = 'votre mot de passe a bien été mis à jour';
                $email = $user->getUserIdentifier();
                $mailer->sendEmail($mailerinterface, $mailMessage, $email);
                $userRepository->save($user, true);
                //return $this->redirectToRoute('app_client_pass', [], Response::HTTP_SEE_OTHER);
                $notification = "Votre mot de passe a été mis à jour.";
                }else{
                $notification="Votre mot de passe acctuel n'est pas le bon. ";
            }
        }

        return $this->renderForm('client/pass.html.twig', [
            'user' => $user,
            'form' => $form,
            'notification'=> $notification,
        ]);
    }

}
