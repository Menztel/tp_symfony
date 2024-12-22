<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\Chapter;
use App\Form\ExerciseType;
use App\Repository\ExerciseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/exercise')]
class ExerciseController extends AbstractController
{
    #[Route('/', name: 'app_exercise_index', methods: ['GET'])]
    public function index(ExerciseRepository $exerciseRepository): Response
    {
        return $this->render('exercise/index.html.twig', [
            'exercises' => $exerciseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_exercise_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exercise = new Exercise();
        
        // Si un chapitre est spécifié dans l'URL, on le préremplit
        if ($request->query->has('chapter')) {
            $chapter = $entityManager->getRepository(Chapter::class)->find($request->query->get('chapter'));
            if ($chapter) {
                $exercise->setChapter($chapter);
            }
        }

        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exercise);
            $entityManager->flush();

            $this->addFlash('success', 'L\'exercice a été créé avec succès.');
            return $this->redirectToRoute('app_chapter_show', ['id' => $exercise->getChapter()->getId()]);
        }

        return $this->render('exercise/new.html.twig', [
            'exercise' => $exercise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exercise_show', methods: ['GET'])]
    public function show(Exercise $exercise): Response
    {
        return $this->render('exercise/show.html.twig', [
            'exercise' => $exercise,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_exercise_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function edit(Request $request, Exercise $exercise, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est le professeur de la matière ou un admin
        if (!$this->isGranted('ROLE_ADMIN') && $exercise->getChapter()->getSubject()->getTeacher() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet exercice.');
        }

        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'exercice a été modifié avec succès.');
            return $this->redirectToRoute('app_chapter_show', ['id' => $exercise->getChapter()->getId()]);
        }

        return $this->render('exercise/edit.html.twig', [
            'exercise' => $exercise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exercise_delete', methods: ['POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function delete(Request $request, Exercise $exercise, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est le professeur de la matière ou un admin
        if (!$this->isGranted('ROLE_ADMIN') && $exercise->getChapter()->getSubject()->getTeacher() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet exercice.');
        }

        if ($this->isCsrfTokenValid('delete'.$exercise->getId(), $request->request->get('_token'))) {
            $chapterId = $exercise->getChapter()->getId();
            $entityManager->remove($exercise);
            $entityManager->flush();

            $this->addFlash('success', 'L\'exercice a été supprimé avec succès.');
            return $this->redirectToRoute('app_chapter_show', ['id' => $chapterId]);
        }

        return $this->redirectToRoute('app_exercise_show', ['id' => $exercise->getId()]);
    }
}
