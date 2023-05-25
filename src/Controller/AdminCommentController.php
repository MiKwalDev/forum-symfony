<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    private $entityManager;
    private $repo;

    public function __construct(EntityManagerInterface $entityManager, CommentRepository $repo)
    {
        $this->entityManager = $entityManager;
        $this->repo = $repo;
    }

    #[Route('/admin/comment/delete/{comment}', name: 'app_admin_comment_delete')]
    public function destroy(Comment $comment, Request $request): Response
    {
        $csrfToken = $request->request->get('_csrf_token');

        if(!$this->isCsrfTokenValid('delete_comment_' . $comment->getId(), $csrfToken)) {
            $this->addFlash("error", "Interdit");
            return $this->redirectToRoute('app_subject', ['subject' => $comment->getSubject()->getId()]);
        }

        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_subject', ['subject' => $comment->getSubject()->getId()]);
    }
}
