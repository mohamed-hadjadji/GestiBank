<?php

namespace App\Controller;

use App\Entity\Chequier;
use App\Form\ChequierType;
use App\Repository\ChequierRepository;
use App\Repository\CompteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ChequierController extends AbstractController
{
    
    #[Route('/client/chequier', name: 'app_chequier_index', methods: ['GET'])]
    public function index(ChequierRepository $chequierRepository): Response
    {
        return $this->render('chequier/index.html.twig', [
            'chequiers' => $chequierRepository->findBy(array("user" => $this->getUser()->getId())),
        ]);
    }

    #[Route('/agent/chequier', name: 'app_chequier_indexA', methods: ['GET'])]
    public function indexA(ChequierRepository $chequierRepository): Response
    {
        return $this->render('chequier/indexA.html.twig', [
            'chequiers' => $chequierRepository->findBy(array("id_agent" => $this->getUser()->getId())),
        ]);
    }

    #[Route('/client/chequier/new', name: 'app_chequier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ChequierRepository $chequierRepository, CompteRepository $compteRepository): Response
    {
        $chequier = new Chequier();
        $comptes = $compteRepository->findBy(array("user" => $this->getUser()->getId()));
        $form = $this->createForm(ChequierType::class, $chequier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idAgent = $request->get('idAgent');
            $compte = $request->get('compte');
            $chequier->setEtat('En-cours');
            $chequier->setDate(new \DateTimeImmutable());
            $chequier->setUser($this->getUser());
            $chequier->setCompte($compte);
            $chequier->setIdAgent($idAgent);
            $chequierRepository->save($chequier, true);

            return $this->redirectToRoute('app_chequier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chequier/new.html.twig', [
            'chequier' => $chequier,
            'form' => $form,
            'comptes' => $comptes
        ]);
    }

    #[Route('client/chequier/{id}', name: 'app_chequier_show', methods: ['GET'])]
    public function show(Chequier $chequier): Response
    {
        return $this->render('chequier/show.html.twig', [
            'chequier' => $chequier,
        ]);
    }

    #[Route('agent/chequier/{id}/edit', name: 'app_chequier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chequier $chequier, ChequierRepository $chequierRepository): Response
    {
        $chequier->setEtat('Valider');
            $chequierRepository->save($chequier, true);
            return $this->redirectToRoute('app_chequier_indexA', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('client/chequier/{id}', name: 'app_chequier_delete', methods: ['POST'])]
    public function delete(Request $request, Chequier $chequier, ChequierRepository $chequierRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chequier->getId(), $request->request->get('_token'))) {
            $chequierRepository->remove($chequier, true);
        }

        return $this->redirectToRoute('app_chequier_index', [], Response::HTTP_SEE_OTHER);
    }
}
