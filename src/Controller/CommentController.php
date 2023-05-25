<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private $entityManager;
    private $repo;

    public function __construct(EntityManagerInterface $entityManager, CommentRepository $repo)
    {
        $this->entityManager = $entityManager;
        $this->repo = $repo;
    }
    
    #[Route('/comment/edit/{comment}', name: 'app_comment_edit')]
    public function index(Request $request, Comment $comment): Response
    {
        $this->denyAccessUnlessGranted('edit', $comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setEditedAt(new \DateTimeImmutable('now'), new DateTimeZone('Europe/Paris'));
            
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_subject', ['subject' => $comment->getSubject()->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
