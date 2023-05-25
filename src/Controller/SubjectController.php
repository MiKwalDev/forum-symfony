<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Subject;
use App\Entity\Tag;
use App\Entity\Vote;
use App\Form\CommentType;
use App\Form\SubjectType;
use App\Form\VoteType;
use App\Repository\SubjectRepository;
use App\Repository\TagRepository;
use App\Repository\VoteRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubjectController extends AbstractController
{
    private $entityManager;
    private $repo;

    public function __construct(EntityManagerInterface $entityManager, SubjectRepository $repo)
    {
        $this->entityManager = $entityManager;
        $this->repo = $repo;
    }

    #[Route('/subject/new', name: 'app_subject_new')]
    public function index(Request $request, TagRepository $tagRepo): Response
    {
        $subject = new Subject;
        $comment = new Comment;

        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $subject->setCreatedAt(new \DateTimeImmutable('now'), new DateTimeZone('Europe/Paris'));
            $subject->setUser($this->getUser());

            if (isset($request->get('subject')['use_tags'])) {
                foreach ($request->get('subject')['use_tags'] as $useTag) {
                    $subject->addTag($tagRepo->find($useTag));
                }
            }

            $comment->setContent($request->get('subject')['comment']['content']);
            $comment->setCreatedAt(new \DateTimeImmutable('now'), new DateTimeZone('Europe/Paris'));
            $comment->setEditedAt(new \DateTimeImmutable('now'), new DateTimeZone('Europe/Paris'));
            $comment->setUser($this->getUser());

            $subject->addComment($comment);

            $this->entityManager->persist($subject);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            if (isset($request->get('subject')['tags'])) {
                foreach ($request->get('subject')['tags'] as $tag) {
                    $newTag = $tagRepo->findByName($tag['name']);
                    $newTag->addSubject($subject);
                    $this->entityManager->persist($newTag);
                }
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('app_subject', ['subject' => $subject->getId()]);
        }

        return $this->render('subject/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/subject/{subject}', name: 'app_subject')]
    public function show(Subject $subject, Request $request, VoteRepository $voteRepo): Response
    {
        $comment = new Comment;

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setCreatedAt(new \DateTimeImmutable('now'), new DateTimeZone('Europe/Paris'));
            $comment->setEditedAt(new \DateTimeImmutable('now'), new DateTimeZone('Europe/Paris'));
            $comment->setUser($this->getUser());
            $subject->addComment($comment);
            
            $this->entityManager->persist($comment);
            $this->entityManager->persist($subject);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_subject', ['subject' => $subject->getId()]);
        }

        $vote = new Vote;

        $voteForm = $this->createForm(VoteType::class, $vote);
        $voteForm->handleRequest($request);

        if ($voteForm->isSubmitted() && $voteForm->isValid()) {

            $currentUser = $this->getUser();
            $existingVote = $voteRepo->findOneBySubjectAndUser($subject, $currentUser);
            
            if ($existingVote) {
                $this->addFlash("error", "Vous avez déjà voté sur ce topic");
                return $this->redirectToRoute('app_subject', ['subject' => $subject->getId()]);
            }
            else {
                if (array_key_exists("upVote", $request->get('vote'))) $vote->setIsUp(true);
                if (array_key_exists("downVote", $request->get('vote'))) $vote->setIsUp(false);
                $vote->setUser($this->getUser());
                $subject->addVote($vote);
    
                $this->entityManager->persist($vote);
                $this->entityManager->persist($subject);
                $this->entityManager->flush();
    
                return $this->redirectToRoute('app_subject', ['subject' => $subject->getId()]);
            }
            
        }

        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
            'form' => $form->createView(),
            'voteForm' => $voteForm->createView(),
        ]);
    }
}
