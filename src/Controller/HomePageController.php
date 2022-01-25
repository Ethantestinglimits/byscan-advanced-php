<?php

namespace App\Controller;

use App\Entity\Manga;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(ManagerRegistry $doctrine): Response
    {
        $mangas = $doctrine->getRepository(Manga::class)->findAll();
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomePageController',
            'mangas' => $mangas
        ]);
    }
}
