<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    /**
     * @Route("/service/{name}", name="service")
     */
    public function showService($name): Response
    {
        return $this->render('service/index.html.twig', [
            'name' => $name,
        ]);
    }
       /**
     * @Route("/go-to-index", name="go_to_index")
     */
    public function goToIndex(): Response
    {
        return $this->redirectToRoute('home');
    }
}
