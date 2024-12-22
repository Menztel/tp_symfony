<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Exercise;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/new/{exercise}', name: 'app_comment_new', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Exercise $exercise, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setExercise($exercise);
        $comment->setAuthor($this->getUser());
        $comment->setContent($request->request->get('content'));
        $comment->setCreatedAt(new \DateTimeImmutable());

        if (empty($comment->getContent())) {
            $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
            return $this->redirectToRoute('app_exercise_show', ['id' => $exercise->getId()]);
        }

        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Votre commentaire a été publié.');
        return $this->redirectToRoute('app_exercise_show', ['id' => $exercise->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est l'auteur du commentaire ou un admin
        if (!$this->isGranted('ROLE_ADMIN') && $comment->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce commentaire.');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été modifié avec succès.');
            return $this->redirectToRoute('app_exercise_show', ['id' => $comment->getExercise()->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est l'auteur du commentaire, le professeur de la matière ou un admin
        if (!$this->isGranted('ROLE_ADMIN') && 
            $comment->getAuthor() !== $this->getUser() && 
            $comment->getExercise()->getChapter()->getSubject()->getTeacher() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce commentaire.');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $exerciseId = $comment->getExercise()->getId();
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');
            return $this->redirectToRoute('app_exercise_show', ['id' => $exerciseId]);
        }

        return $this->redirectToRoute('app_exercise_show', ['id' => $comment->getExercise()->getId()]);
    }
}
