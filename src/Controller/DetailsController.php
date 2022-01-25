<?php


namespace App\Controller;

use App\Entity\Manga;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;

class DetailsController extends AbstractController
{
    /**
     * @Route("/manga/{id}", name="details")
     */
    public function index(Manga $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $parameter = $this->getParameter('code');
        $manga = $id;
        $delete = $this->createFormBuilder()
            ->add(
                'admincode',
                PasswordType::class,
                ['constraints' => [new EqualTo($parameter, null, "Le code n'est pas bon")]]
            )
            ->add('supression', SubmitType::class, ['label' => 'supression'])
            ->getForm();
                

        $delete->handleRequest($request);

        if ($delete->isSubmitted() && $delete->isValid()) {
             $delete->getData();
             $doctrine->getManager()->remove($manga);
            $doctrine->getManager()->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('details/details.html.twig', [
            'controller_name' => 'DetailsController',
            'manga' => $id,
            'delete' => $delete->createView()
        ]);
    }
}
