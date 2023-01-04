<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Entity\News;
use App\Form\ConfigurationType;
use App\Repository\ConfigurationRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

// #[Route('/configuration')]
class ConfigurationController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(ConfigurationRepository $configurationRepository, NewsRepository $news_repository): Response
    {
        $configurations = $configurationRepository->findAll();
        $news = $news_repository->findAll(); 
    
        return $this->render('accueil/index.html.twig', [
            'configurations' => $configurations,
            'news'=>$news
        ]);
    }
    

    #[Route('configuration/new', name: 'app_configuration_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ConfigurationRepository $configurationRepository): Response
    {
        $configuration = new Configuration();
        $form = $this->createForm(ConfigurationType::class, $configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $configurationRepository->save($configuration, true);

            return $this->redirectToRoute('app_configuration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('configuration/new.html.twig', [
            'configuration' => $configuration,
            'form' => $form,
        ]);
       
    }

    #[Route('configuration/{id}', name: 'app_configuration_show', methods: ['GET'])]
    public function show(Configuration $configuration): Response
    {
        return $this->render('configuration/show.html.twig', [
            'configuration' => $configuration,
        ]);
    }

    #[Route('/admin/configuration/{id}/edit', name: 'app_configuration_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Configuration $configuration , ConfigurationRepository $configurationRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ConfigurationType::class, $configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          

            $image = $form->get('logo')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                /* On renome */
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                /* dd($newFilename); */
                // Move the file to the directory where brochures are stored
                /* On déplace le fichier dans notre serveur */
                try {
                    $image->move(
                        $this->getParameter('configuration_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'imagename' property to store the PDF file name
                // instead of its contents
                $configuration->setLogo($newFilename);
            }
            $configurationRepository->save($configuration, true);

            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('configuration/edit.html.twig', [
            'configuration' => $configuration,
            'form' => $form->createView()
        ]);
    }

    #[Route('configuration/{id}', name: 'app_configuration_delete', methods: ['POST'])]
    public function delete(Request $request, Configuration $configuration, ConfigurationRepository $configurationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$configuration->getId(), $request->request->get('_token'))) {
            $configurationRepository->remove($configuration, true);
        }

        return $this->redirectToRoute('app_configuration_index', [], Response::HTTP_SEE_OTHER);
    }
}
