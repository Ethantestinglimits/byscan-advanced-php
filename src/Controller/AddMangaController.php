<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Form\AddMangaType;
use App\Service\MangaScrapper;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\File;


class AddMangaController extends AbstractController 
{
    /**
     * @Route("/manga/add")
     */

    public function add(Request $request, ManagerRegistry $doctrine, MangaScrapper $scrape)
    {

        $manga = new Manga();
        $form = $this->createForm(AddMangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manga = $form->getData();
            $entityManager = $doctrine->getManager();
            $manga = $scrape->scrapeDescription($manga);
            if ($manga != null) {

                /** @var Manga $old */
                $old = $entityManager->getRepository(Manga::class)->findOneBy(['name' => $manga->getName()]);

                if ($old !== null) {
                    $entityManager->persist($old);
                    $entityManager->remove($manga);
                    $entityManager->detach($manga);
                    $entityManager->flush();
                    $this->addFlash("info", "Manga déja existant, ajout de la note");
                } else {
                    $entityManager->persist($manga);
                    $entityManager->flush();
                    $this->addFlash("succes", "Le manga a bien été ajouté");
                }

                return $this->redirectToRoute('home');
            } else {
                $form->addError(new FormError('Le manga n\'existe pas'), "name");
            }
        }
        return $this->renderForm('add/add.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/manga/add/csv")
     */
    public function csv(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer, MangaScrapper $scrape): Response
    {
        $form = $this->createFormBuilder()
            ->add('fichier', FileType::class,['constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'text/csv',
                        'text/plain'
                        ],
                    'mimeTypesMessage' => 'Merci de donner un fichier CSV (type rentré: {{ type }})',
                    'maxSizeMessage' => 'Le fichier est trop gros (taille maximal: {{ limit }} {{ suffix }}).'
                ])]])
            ->add('save', SubmitType::class, ['label' => 'Ajouter'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $doctrine->getManager();

            /** @var UploadedFile $file */

            $file = $form['fichier']->getData();
            
            if (($mangas = fopen($file->getRealPath(), 'r')) !== false) {
                
                $i = 1;

                $data = fgetcsv($mangas, 1000, ';');
                $size = count($data);
                
                while ($size > $i) {
                    
                    $manga = new Manga();
                    $manga->setName($data[$i]);
                    $manga = $scrape->scrapeDescription($manga);   
                    // dd($manga);

                    if ($manga != null) {

                        /** @var Manga $old */
                        $old = $entityManager->getRepository(Manga::class)->findOneBy(['name' => $manga->getName()]);
                        // dd($old);
                        if ($old !== null) {
                            $entityManager->persist($old);
                            $entityManager->remove($manga);
                            $entityManager->detach($manga);
                            $entityManager->flush();
                            $this->addFlash("info", "Manga déja existant");
                        } else {
                            $manga->setAddedBy('admin@byscan.com');
                            $entityManager->persist($manga);
                            $entityManager->flush();
                            $this->addFlash("succes", "Le manga a bien été ajouté");
                        }
        
                    } else {
                        $this->addFlash("error",'Le manga '. $data[$i] .'n\'existe pas');
                    }

                    $i++;
                }

                fclose($mangas);
                $entityManager->flush();
                $this->addFlash("succes", "Les mangas ont bien été ajoutés");
            }

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('add/csv.html.twig', [
            'form' => $form,
        ]);
    }


}