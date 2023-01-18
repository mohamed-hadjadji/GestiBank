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

#[Route('client/chequier')]
class ChequierController extends AbstractController
{
    #[Route('/', name: 'app_chequier_index', methods: ['GET'])]
    public function index(ChequierRepository $chequierRepository): Response
    {
        return $this->render('chequier/index.html.twig', [
            'chequiers' => $chequierRepository->findBy(array("user" => $this->getUser()->getId())),
        ]);
    }

    // #[Route('/', name: 'app_chequier_index', methods: ['GET'])]
    // public function index(ChequierRepository $chequierRepository): Response
    // {
    //     return $this->render('chequier/index.html.twig', [
    //         'chequiers' => $chequierRepository->findAll(),
    //     ]);
    // }

    #[Route('/new', name: 'app_chequier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ChequierRepository $chequierRepository, CompteRepository $compteRepository): Response
    {
        $chequier = new Chequier();
        $comptes = $compteRepository->findBy(array("user" => $this->getUser()->getId()));
        $form = $this->createForm(ChequierType::class, $chequier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte = $request->get('compte');
            $chequier->setEtat('En-cours');
            $chequier->setDate(new \DateTimeImmutable());
            $chequier->setUser($this->getUser());
            $chequier->setCompte($compte);
            $chequierRepository->save($chequier, true);

            return $this->redirectToRoute('app_chequier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chequier/new.html.twig', [
            'chequier' => $chequier,
            'form' => $form,
            'comptes' => $comptes
        ]);
    }

    #[Route('/{id}', name: 'app_chequier_show', methods: ['GET'])]
    public function show(Chequier $chequier): Response
    {
        return $this->render('chequier/show.html.twig', [
            'chequier' => $chequier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chequier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chequier $chequier, ChequierRepository $chequierRepository): Response
    {
        $form = $this->createForm(ChequierType::class, $chequier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chequierRepository->save($chequier, true);

            return $this->redirectToRoute('app_chequier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chequier/edit.html.twig', [
            'chequier' => $chequier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chequier_delete', methods: ['POST'])]
    public function delete(Request $request, Chequier $chequier, ChequierRepository $chequierRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chequier->getId(), $request->request->get('_token'))) {
            $chequierRepository->remove($chequier, true);
        }

        return $this->redirectToRoute('app_chequier_index', [], Response::HTTP_SEE_OTHER);
    }
}
