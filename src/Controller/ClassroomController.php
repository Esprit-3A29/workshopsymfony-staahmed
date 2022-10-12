<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Form\ClassroomType;
use App\Repository\ClassroomRepository;
use Doctrine\Persistence\ManagerRegistry;
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

    #[Route('/classroom', name:'app_classroom')]

 
    public function Classroom(ClassroomRepository $repository)
    {
        $Classroom = $repository->findAll();


        return $this->render("classroom/Classroom.html.twig", ["Classroom" => $Classroom]);
    }


    
    #[Route('/delete/{id}', name:'app_delete')]
    public function delete($id, ClassroomRepository $repository)
    {
        $Crud = $repository->find($id);
        
        $em=$this->getDoctrine()->getManager();
        $em->remove($Crud);
        $em->persist($Crud);
        $em->flush();
    
       return $this->redirectToRoute('classroom');
    }
 

    

    #[Route('/removeForm/{id}', name: 'remove')]

    public function removeStudent(ManagerRegistry $doctrine,$id,ClassroomRepository $repository)
    {
        $student= $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($student);
        $em->flush();
        return  $this->redirectToRoute("classroom");
    }

    #[Route('/addForm', name: 'add2')]
    public function addForm(ManagerRegistry $doctrine,Request $request)
    {
        $Classroom= new Classroom;
        $form= $this->createForm(ClassroomType::class,$Classroom);
        $form->handleRequest($request) ;
        if ($form->isSubmitted()){
             $em= $doctrine->getManager();
             $em->persist($Classroom);
             $em->flush();
             return  $this->redirectToRoute("classroom");
         }
        return $this->renderForm("Classroom/add.html.twig",array("formClassroom"=>$form));
    }
    #[Route('/add', name:'add')]
    public function add(Request $request)
    {
        $Classroom = new Classroom();
        $form = $this->createForm(ClassroomType::class, $Classroom);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($Classroom);
            $em->flush();
            return $this->redirectToRoute('Classroom');
        }
        return $this->render("Classroom/Classroom.html.twig", ["form" => $form->createView()]);
    }

  
    
    #[Route('/update/{id}', name: 'app_update')]
   
    public function update($id, Request $request, ClassroomRepository $repository): Response
    {
        $Crud = $repository->find($id);
        $form = $this->createForm(CrudType::class, $Crud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($Crud);
            $em->flush();
            return $this->redirectToRoute('classroom');
        }

        return $this->render("classroom/update.html.twig", ["form" => $form->createView()]);
    }

 


}
