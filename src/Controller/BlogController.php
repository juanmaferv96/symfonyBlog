<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Noticia;


class BlogController extends AbstractController
{
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $noticias = $entityManager->getRepository(Noticia::class)->findAll();
        return $this->render('index.html.twig', [
            //'title' => 'Inicio',
            'noticias' => $noticias
        ]);
    }

    public function noticia($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $noticia = $entityManager->getRepository(Noticia::class)->find($id);

        if (!$noticia){
            throw $this->createNotFoundException(
                'No existe ninguna noticia con id '.$id
            );
        }

        return $this->render('noticia.html.twig', [
            'noticia' => $noticia
        ]);
    }

    public function nuevaNoticia(Request $request)
    {
        $noticia = new Noticia();
 

        $form = $this->createFormBuilder($noticia)
            ->add('titular', TextType::class)
            ->add('entradilla', TextareaType::class, array('required' => false))
            ->add('cuerpo', TextareaType::class, array('required' => false))
            ->add('save', SubmitType::class,
                array('label' => 'Añadir Noticia'))
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $noticia = $form->getData();
    
                $entityManager = $this->getDoctrine()->getManager();
    
                $noticia->setFecha(new \DateTime('now'));

                $entityManager->persist($noticia);
    
                $entityManager->flush();
    
                return $this->redirectToRoute('noticiaCreada');
            }

        return $this->render('nuevaNoticia.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function noticiaCreada(): Response
    {
        return $this->render('noticiaCreada.html.twig');
    }

    public function editarNoticia(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $noticia = $entityManager->getRepository(Noticia::class)->find($id);

        if (!$noticia){
            throw $this->createNotFoundException(
                'No existe ninguna noticia con id '.$id
            );
        }

        $form = $this->createFormBuilder($noticia)
        ->add('titular', TextType::class)
        ->add('entradilla', TextareaType::class, array('required' => false))
        ->add('cuerpo', TextareaType::class, array('required' => false))
        ->add('save', SubmitType::class,
            array('label' => 'Editar Noticia'))
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $noticia = $form->getData();

            $entityManager->flush();

            return $this->redirectToRoute('noticia', array('id'=>$id));
        }

        return $this->render('nuevaNoticia.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function borrarNoticia($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $noticia= $entityManager->getRepository(Noticia::class)->find($id);

        
        if (!$noticia){
            throw $this->createNotFoundException(
                'No existe ninguna noticia con id '.$id
            );
        }
        $entityManager->remove($noticia);
        $entityManager->flush();
        return $this->render('noticiaBorrada.html.twig');
    }
}
    
