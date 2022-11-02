<?php

namespace App\Controller;
use App\Form\SearchStudentType;
use App\Entity\Student;
use App\Form\StudentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\StudentRepository;
use App\Repository\ClassroomRepository;
use Doctrine\Persistence\ManagerRegistry;
class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }

  
    #[Route('/listStudent', name: 'list_student')]
    public function listStudent(Request $request,StudentRepository $repository)
    {
        $students= $repository->findAll();
       // $students= $this->getDoctrine()->getRepository(StudentRepository::class)->findAll();
        $sortStudent= $repository->sortStudent();
       $formSearch= $this->createForm(SearchStudentType::class);
       $formSearch->handleRequest($request);
       $topStudents= $repository->topStudent();
       if($formSearch->isSubmitted()){
           $nce= $formSearch->get('nce')->getData();
           //var_dump($nce).die();
           $result= $repository->searchStudent($nce);
           return $this->renderForm("student/list.html.twig",
               array("tabStudent"=>$result,
                   "sortStudent"=>$sortStudent,
                   "searchForm"=>$formSearch,
                   'topStudents'=>$topStudents));
       }
         return $this->renderForm("student/list.html.twig",
           array("tabStudent"=>$students,
               "sortStudent"=>$sortStudent,
                "searchForm"=>$formSearch,
               'topStudents'=>$topStudents));
    }



    #[Route('/addstudent', name: 'app_addstudent')]
    public function addStudent(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request)
    {
        $student= new Student();
        $form= $this->createForm(StudentType::class,$student);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $em = $doctrine->getManager();
            $em->persist($student);
            $em->flush();
            return  $this->redirectToRoute("app_addstudent");
        }
        return $this->renderForm("student/add.html.twig",
            array("formStudent"=>$form));

    }

    #[Route('/updateForm/{nce}', name: 'update')]
    public function  updateForm($nce,StudentRepository $repository,ManagerRegistry $doctrine,Request $request)
    {
        $student= $repository->find($nce);
        $form= $this->createForm(StudentType::class,$student);
        $form->handleRequest($request) ;
        if ($form->isSubmitted()){
            $em= $doctrine->getManager();
            $em->flush();
            return  $this->redirectToRoute("list_student");
        }
        return $this->renderForm("student/update.html.twig",array("formStudent"=>$form));
    }

    #[Route('/removeForm/{nce}', name: 'remove')]

    public function removeStudent(ManagerRegistry $doctrine,$nce,StudentRepository $repository)
    {
        $student= $repository->find($nce);
        $em = $doctrine->getManager();
        $em->remove($student);
        $em->flush();
        return  $this->redirectToRoute("list_student");
    }

    #[Route('/deleteC/{id}', name: 'deleteC')]
    public function deleteClassroom($id,ManagerRegistry $doctrine,ClassroomRepository $repository)
    {
        $classroom= $repository->find($id);
        $em= $doctrine->getManager();
        $em->remove($classroom);
        $em->flush();
        return new Response("hello");
    }

}