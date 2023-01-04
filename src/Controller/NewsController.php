<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use Doctrine\ORM\EntityManager;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/news')]
class NewsController extends AbstractController
{
    #[Route('/views', name: 'app_actualities_index', methods: ['GET'])]
    public function view(NewsRepository $news_repository,$id): Response
    {
       $news = $news_repository->find($id);

        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }
    #[Route('/', name: 'app_news_index', methods: ['GET','POST'])]
    public function index(NewsRepository $news_repository, Request $request, PaginatorInterface $paginator): Response
    {
        $news = $news_repository->findAll();
            $pages = $paginator->paginate(
                $news,
                $request->query->getInt('page', 1),
                3
            );
        
        return $this->render('news/index.html.twig', [
            'news' => $pages,
        ]);
    }

    #[Route('/add', name: 'app_news_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $new = new News();
        $form = $this->createForm(NewsType::class, $new);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $original_filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safe_filename = $slugger->slug($original_filename);
                $new_filename = $safe_filename.'-'.uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $new_filename
                    );
                } catch (FileException $e) {
                }
                $new->setImage($new_filename);
            }

            $new = $form->getData();
            $manager->persist($new);
            $manager->flush();
            $this->addFlash('success','L\'actualité a été ajoutée avec succès');

            return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('news/add.html.twig', ["form"=>$form->createView()]);
        
    }

    #[Route('/{id}', name: 'app_news_show', methods: ['GET'])]
    public function show(News $news): Response
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_news_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, News $new, SluggerInterface $slugger, EntityManagerInterface $entity_manager): Response
    {
        $form = $this->createForm(NewsType::class, $new);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $original_filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safe_filename = $slugger->slug($original_filename);
                $new_filename = $safe_filename.'-'.uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $new_filename
                    );
                } catch (FileException $e) {
                }
                $new->setImage($new_filename);
            }
            $new = $form->getData();
            $entity_manager->persist($new);
            $entity_manager->flush();
            $this->addFlash('success','L\'actualité a été mise à jour avec succès');

            return $this->redirectToRoute('app_news_index');
        
        }  

        return $this->render('news/add.html.twig', ["form"=>$form->createView()]);


       ;
    }

    #[Route('/{id}/delete', name: 'app_news_delete', methods: ['POST','GET'])]
    public function delete(EntityManagerInterface $entity_manager, News $new): Response
    {
        $entity_manager->remove($new);
        $entity_manager->flush();
        $this->addFlash('success','L\'actualité a été supprimée avec succès');


        return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
    }
}
