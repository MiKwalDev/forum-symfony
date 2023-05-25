<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    private $entityManager;
    private $userRepo;
    private $subjectRepo;
    private $commentRepo;
    private $voteRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepo,
        SubjectRepository $subjectRepo,
        CommentRepository $commentRepo,
        VoteRepository $voteRepo
        )
    {
        $this->entityManager = $entityManager;
        $this->userRepo = $userRepo;
        $this->subjectRepo = $subjectRepo;
        $this->commentRepo = $commentRepo;
        $this->voteRepo = $voteRepo;
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        $usersCount = count($this->userRepo->findAll());
        $subjectsCount = count($this->subjectRepo->findAll());
        $commentsCount = count($this->commentRepo->findAll());
        $votesCount = count($this->voteRepo->findAll());
        $users = $this->userRepo->findBy([], ["created_at" => "DESC"], 3);
        

        return $this->render('admin_dashboard/index.html.twig', [
            "usersCount" => $usersCount,
            "subjectsCount" => $subjectsCount,
            "commentsCount" => $commentsCount,
            "votesCount" => $votesCount,
            "users" => $users
        ]);
    }
}
