<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\User;
use App\Form\DemandeType;
use App\Repository\ConfigurationRepository;
use App\Repository\DemandeRepository;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use App\Services\MailerService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Currency;

class AccueilController extends AbstractController
{

    #[Route('/cours', name: 'cours')]
    public function conversion_devise(Request $request,Currency $currency): Response
    {
        $resultat="";
        if($request->getMethod()=="POST")
        {
            $devise_depart="USD";
            $devise_arrive=$request->get('devisearrive');
            $montant=$request->get('montant');
            $resultat = $currency->conversion($devise_depart,$devise_arrive,$montant);
        }
        return new Response($resultat);
    }



    // Fonction qui permet à l'utilisateur de demander une ouverture de compte
    #[Route('/demande-ouverture-compte', name: 'app_demande', methods: ['GET', 'POST'])]
    public function new(NewsRepository $news_repository, Request $request, DemandeRepository $demandeRepository, ConfigurationRepository $configurationRepository): Response
    {
        $news = $news_repository->findAll();

        $configurations = $configurationRepository->findAll();
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // upload de fichier va se faire ici
            $photoFile = $form->get('photo')->getData();
            $identiteFile = $form->get('identite')->getData();
            $this->addFlash('notice', 'Merci de nous avoir contacté. Notre équipe va vous répondre dans les meilleurs delais.');

            if ($photoFile && $identiteFile) {
                $originalphotoFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newPhotoFilename = $originalphotoFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                $originalidentiteFilename = pathinfo($identiteFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newIdentiteFilename = $originalidentiteFilename.'-'.uniqid().'.'.$identiteFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $photoFile->move($this->getParameter('photos_directory'),$newPhotoFilename);

                    $identiteFile->move($this->getParameter('identites_directory'),$newIdentiteFilename);
                } catch (FileException $e) {
                    $e->getMessage('error');
                }
                $demande->setPhoto($newPhotoFilename);
                $demande->setIdentite($newIdentiteFilename);
                $demande->setDatedemande(new \DateTimeImmutable());
                $demande->setIdUser($this->getUser());
            }

            // fin upload de fichier
            $demandeRepository->save($demande, true);

            return $this->redirectToRoute('app_demande', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande/new.html.twig', [
            'demande' => $demande,
            'form' => $form,
            'configurations' => $configurations,
            'agents' => '',
            'news' => $news,
        ]);
    }
}
