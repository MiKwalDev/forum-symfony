<?php

namespace App\Controller;

use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(SubjectRepository $subRepo): Response
    {
        $subjects = $subRepo->findAll();

        return $this->render('home/index.html.twig', [
            'subjects' => $subjects,
        ]);
    }
}
