<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /**
     * @Route("/stats", name="stats")
     */
    public function index(MangaRepository $mangaRepository): Response
    {

        $mangas = $mangaRepository->findAll();

        return $this->render('stats/stats.html.twig', [
            'mangas' => $mangas,
        ]);
    }
}
