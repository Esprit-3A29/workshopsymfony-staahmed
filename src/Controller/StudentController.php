<?php

namespace App\Controller;

use App\Form\SearchStudentType;
use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $sortByMoyenne= $repository->sortByMoyenne();
       $formSearch= $this->createForm(SearchStudentType::class);
       $formSearch->handleRequest($request);
       $topStudents= $repository->topStudent();
       if($formSearch->isSubmitted()){
           $nce= $formSearch->get('nce')->getData();
           //var_dump($nce).die();
           $result= $repository->searchStudent($nce);
           return $this->renderForm("student/listStudent.html.twig",
               array("tabStudent"=>$result,
                   "sortByMoyenne"=>$sortByMoyenne,
                   "searchForm"=>$formSearch));
       }
         return $this->renderForm("student/listStudent.html.twig",
           array("tabStudent"=>$students,
               "sortByMoyenne"=>$sortByMoyenne,
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


           // sortByMoyenne->$repository=sortByMoyenne ;


    }
    public function sortByMoyenne() {
        $qb=  $this->createQueryBuilder('x')
            ->orderBy('x.moyenne','ASC');
        return $qb ->getQuery()
            ->getResult();
    }



    public function getStudentsByClassroom($id)  {
        $qb= $this->createQueryBuilder('s')
            ->join('s.classroom','c')
            ->addSelect('c')
            ->where('c.id=:id')
            ->setParameter('id',$id);
        return $qb->getQuery()
            ->getResult();
    }

    public function searchStudent($nce) {
        $qb=  $this->createQueryBuilder('s')
            ->where('s.nce LIKE :x')
            ->setParameter('x',$nce);
        return $qb->getQuery()
            ->getResult();
    }

    public function searchByMoyenne($min,$max) :array {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT s FROM App\Entity\Student s WHERE s.moyenne BETWEEN :min AND :max')
            ->setParameter('min',$min)
            ->setParameter('max',$max);
        return $query->getResult();
    }
}