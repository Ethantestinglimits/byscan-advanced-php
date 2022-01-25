<?php

namespace App\Service;

use App\Entity\Manga;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;

class MangaScrapper extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;

    }

    /**
     * @param Manga $manga
     */

    public function scrapeDescription(Manga $manga): ?Manga
    {
        $client = HttpClient::create();
        $response = $client->request(
            'GET', 
            'https://nautiljon.api.barthofu.com/search',
            [ 'query' => [
                'query' => $manga->getName(), 
                'type' => 'manga', 
                'limit' => 1
                ]
            ]);
        $content = json_decode($response->getContent(), true);

        if ($content) {

            $content = $content[0];
            $response = $client->request(
                'GET', 
                'https://nautiljon.api.barthofu.com/getInfoFromUrl',
                [ 'query' => [
                    'url' => $content['url'], 
                    ]
                ]);
                
            $fetchContent = json_decode($response->getContent(), true);

            $manga->setDescription($content['description']);
            $manga->setCover($fetchContent['imageUrl']);
            $manga->setVotes(intval(str_replace(",", "", $fetchContent['votersNumber'])));
            $manga->setRating(floatval($fetchContent['score']));
            $manga->setName(strval($fetchContent['name']));
            $manga->setJapName($fetchContent['japName']);

            return $manga;
        } else {
            return null;
        }
    }
}
