<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_dashboard_admin')]
    public function indexAdmin(): Response
    {
        $email = $this->getUser()->getUserIdentifier();
        return $this->render('dashboard/index_admin.html.twig', [
            'controller_name' => 'DashboardAdmin',
            'user_email'=> $email
        ]);
    }

    #[Route('/agent', name: 'app_dashboard_agent')]
    public function indexAgent(): Response
    {
        $email = $this->getUser()->getUserIdentifier();
        return $this->render('dashboard/index_agent.html.twig', [
            'controller_name' => 'DashboardAgent',
            'user_email'=> $email
        ]);
    }


    #[Route('/client', name: 'app_dashboard_client')]
    public function indexClient(): Response
    {
        $email = $this->getUser()->getUserIdentifier();
        return $this->render('dashboard/index_client.html.twig', [
            'controller_name' => 'DashboardClient',
            'user_email'=> $email
        ]);
    }
}
