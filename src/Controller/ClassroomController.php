<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Form\ClassroomType;
use App\Repository\ClassroomRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassroomController extends AbstractController
{
    #[Route('/classroom', name: 'app_classroom')]
    public function index(): Response
    {
        return $this->render('classroom/index.html.twig', [
            'controller_name' => 'ClassroomController',
        ]);

        
    }

    #[Route('/listclassroom', name: 'list_classroom')]
    public function listclassroom(classroomRepository $repository)
    {
        $classrooms= $repository->findAll();
       // $classrooms= $this->getDoctrine()->getRepository(classroomRepository::class)->findAll();
       return $this->render("classroom/list.html.twig",array("tabclassroom"=>$classrooms));
    }


    #[Route('/addclassroom', name: 'add_classroom')]
    public function addclassroom(ManagerRegistry $doctrine)
    {
        $classroom= new classroom();
        $classroom->setName("rahma");
        $classroom->setDescription("okay");
       // $em=$this->getDoctrine()->getManager();
        $em= $doctrine->getManager();
        $em->persist($classroom);
        $em->flush();
        return $this->redirectToRoute("list_classroom");
    }

    #[Route('/addForm', name: 'add2')]
    public function addForm(ManagerRegistry $doctrine,Request $request)
    {
        $classroom= new classroom;
        $form= $this->createForm(classroomType::class,$classroom);
        $form->handleRequest($request) ;
        if ($form->isSubmitted()){
             $em= $doctrine->getManager();
             $em->persist($classroom);
             $em->flush();
             return  $this->redirectToRoute("list_classroom");
         }
        return $this->renderForm("classroom/add.html.twig",array("formclassroom"=>$form));
    }

    #[Route('/updateForm/{id}', name: 'update')]
    public function  updateForm($id,classroomRepository $repository,ManagerRegistry $doctrine,Request $request)
    {
        $classroom= $repository->find($id);
        $form= $this->createForm(classroomType::class,$classroom);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em= $doctrine->getManager();
            $em->flush();
            return  $this->redirectToRoute("list_classroom");
        }
        return $this->renderForm("classroom/update.html.twig",array("formclassroom"=>$form));
    }

    #[Route('/removeForm/{id}', name:'remove')]

    public function removeclassroom(ManagerRegistry $doctrine,$id,classroomRepository $repository)
    {
        $classroom= $repository->find($id);
        $em=$doctrine->getManager();
        $em->remove($classroom);
        $em->flush();
        return  $this->redirectToRoute("list_classroom");
    }
}