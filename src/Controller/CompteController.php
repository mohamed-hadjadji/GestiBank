<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Compte;
use App\Entity\User;
use App\Form\CompteType;
use App\Repository\CompteRepository;
use App\Repository\DemandeRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/agent/compte')]
class CompteController extends AbstractController
{
    #[Route('/', name: 'app_compte_index', methods: ['GET'])]
    public function index(CompteRepository $compteRepository): Response
    {
        return $this->render('compte/index.html.twig', [
            'comptes' => $compteRepository->findBy(array("id_agent" => $this->getUser()->getId())),
        ]);
    }

    #[Route('/new', name: 'app_compte_new', methods: ['GET', 'POST'])]

    public function new(Request $request, CompteRepository $compteRepository, UserRepository $userRepository): Response
    {
        $compte = new Compte();
        if ($request->getMethod() == 'POST') {
                $idAgent = $request->get('idAgent');
                $typeCompte= $request->get('typeCompte');
                $client= $userRepository->find($request->get('idClient'));
                $compte->setType($typeCompte);
                $compte->setSolde(0.0);
                $compte->setDateCreation(new \DateTimeImmutable());
                $compte->setIdAgent($idAgent);
                $compte->setUser($client);
                $compte->setNumCompte('4586658'. random_int(9999, 999999));
                $compteRepository->save($compte, true);
        }
            return $this->redirectToRoute('app_compte_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}', name: 'app_compte_show', methods: ['GET'])]
    public function show(Compte $compte): Response
    {
        return $this->render('compte/show.html.twig', [
            'compte' => $compte,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_compte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Compte $compte, CompteRepository $compteRepository): Response
    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $compteRepository->save($compte, true);
            $this->addFlash(type: 'success', message: "Compte édité avec succès");
            return $this->redirectToRoute('app_compte_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('compte/edit.html.twig', [
            'compte' => $compte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_compte_delete', methods: ['POST'])]
    public function delete(Request $request, Compte $compte, CompteRepository $compteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$compte->getId(), $request->request->get('_token'))) {
            $compteRepository->remove($compte, true);
        }

        return $this->redirectToRoute('app_compte_index', [], Response::HTTP_SEE_OTHER);
    }
}
