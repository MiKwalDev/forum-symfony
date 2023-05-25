<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSubjectController extends AbstractController
{
    private $entityManager;
    private $repo;

    public function __construct(EntityManagerInterface $entityManager, SubjectRepository $repo)
    {
        $this->entityManager = $entityManager;
        $this->repo = $repo;
    }
    
    #[Route('/admin/subject', name: 'app_admin_subject')]
    public function index(): Response
    {
        $subjects = $this->repo->findAll();

        return $this->render('admin_subject/index.html.twig', [
            "subjects" => $subjects,
        ]);
    }

    #[Route('/admin/subject/toggle_is_open/{subject}', name: 'app_admin_subject_toggle_is_open', methods: ['POST'])]
    public function toggleIsOpen(Subject $subject)
    {
        $subject->setIsOpen(!$subject->isIsOpen());

        $this->entityManager->persist($subject);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_subject');
    }
}
