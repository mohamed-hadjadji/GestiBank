<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\User;
use App\Form\DemandeType;
use App\Repository\DemandeRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DemandeController extends AbstractController
{
    #[Route('/agent/demande/liste-demande', name: 'app_demande_agent_index', methods: ['GET'])]
    public function indexAgent(DemandeRepository $demandeRepository, ManagerRegistry $doctrine, UserRepository $userRepository): Response
    {

        return $this->render('demande/indexAgent.html.twig', [
            'demandes' => $demandeRepository->findBy(array("agent" => $this->getUser()->getId())),
        ]);
    }

    #[Route('/agent/demande/liste-nouv-clients', name: 'app_new_client_index', methods: ['GET'])]
    public function index_new_cli(DemandeRepository $demandeRepository, ManagerRegistry $doctrine, UserRepository $userRepository): Response
    {

        return $this->render('demande/index_new_cli.html.twig', [
            'demandes' => $demandeRepository->findBy(array("agent" => $this->getUser()->getId())),
        ]);
    }

    #[Route('/agent/demande/liste-clients', name: 'app_client_ag_index', methods: ['GET'])]
    public function index_cli(DemandeRepository $demandeRepository, ManagerRegistry $doctrine, UserRepository $userRepository): Response
    {

        return $this->render('demande/index_client_ag.html.twig', [
            'demandes' => $demandeRepository->findBy(array("agent" => $this->getUser()->getId())),
        ]);
    }

    #[Route('/agent/demande/{id}/modif_role', name: 'app_demande_edit_role', methods: ['GET', 'POST'])]
    public function edit_role(DemandeRepository $demandeRepository, User $user, UserRepository $userRepository): Response
    {
               
            $user->setRoles(array('ROLE_CLIENT'));
            $userRepository->save($user, true);
            return $this->render('demande/index_client_ag.html.twig', [
                'demandes' => $demandeRepository->findBy(array("agent" => $this->getUser()->getId())),
            ]);
        
    }
    
    #[Route('/admin/demande/liste-demande', name: 'app_demande_index', methods: ['GET'])]
    public function index(DemandeRepository $demandeRepository): Response
    {
        return $this->render('demande/index.html.twig', [
            'demandes' => $demandeRepository->findAll(),
        ]);
    }

    #[Route('/admin/demande/{id}', name: 'app_demande_show', methods: ['GET'])]
    public function show(Demande $demande): Response
    {
        return $this->render('demande/show.html.twig', [
            'demande' => $demande,
        ]);
    }

    #[Route('/admin/demande/{id}/modifier', name: 'app_demande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Demande $demande, DemandeRepository $demandeRepository, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(User::class);
        $agents = $repository->findByRole("ROLE_AGENT");
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agent = $request->get('agent');
            $demande->setAgent($agent);
            $demandeRepository->save($demande, true);
            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande/edit.html.twig', [
            'demande' => $demande,
            'form' => $form,
            'agents' => $agents
        ]);
    }

    #[Route('/agent/demande/{id}/modif_etat', name: 'app_demande_edit_etat', methods: ['GET', 'POST'])]
    public function edit_etat(Demande $demande, DemandeRepository $demandeRepository): Response
    {
               
            $demande->setEtat('Valider');
            $demandeRepository->save($demande, true);
            return $this->redirectToRoute('app_demande_agent_index', [], Response::HTTP_SEE_OTHER);
        
    }

    #[Route('/agent/demande/{id}/modif_etat_ref', name: 'app_demande_edit_etat_ref', methods: ['GET', 'POST'])]
    public function edit_etat_ref(Demande $demande, DemandeRepository $demandeRepository): Response
    {
               
            $demande->setEtat('Rejeter');
            $demandeRepository->save($demande, true);
            return $this->redirectToRoute('app_demande_agent_index', [], Response::HTTP_SEE_OTHER);
        
    }

    #[Route('/admin/demande/{id}', name: 'app_demande_delete', methods: ['POST'])]
    public function delete(Request $request, Demande $demande, DemandeRepository $demandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demande->getId(), $request->request->get('_token'))) {
            $demandeRepository->remove($demande, true);
        }

        return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/agent/demande/{id}', name: 'app_demande_agent_delete', methods: ['GET','POST'])]
    public function agent_delete(EntityManagerInterface $entity_manager, Demande $demande): Response
    {
        $entity_manager->remove($demande);
        $entity_manager->flush();
        $this->addFlash('success','Demande supprimer avec succès');

        return $this->redirectToRoute('app_demande_agent_index', [], Response::HTTP_SEE_OTHER);
    }

}

