<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/chapter')]
class ChapterController extends AbstractController
{
    #[Route('/', name: 'app_chapter_index', methods: ['GET'])]
    public function index(ChapterRepository $chapterRepository): Response
    {
        return $this->render('chapter/index.html.twig', [
            'chapters' => $chapterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chapter_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chapter = new Chapter();
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chapter);
            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été créé avec succès.');
            return $this->redirectToRoute('app_subject_show', ['id' => $chapter->getSubject()->getId()]);
        }

        return $this->render('chapter/new.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chapter_show', methods: ['GET'])]
    public function show(Chapter $chapter): Response
    {
        return $this->render('chapter/show.html.twig', [
            'chapter' => $chapter,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chapter_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function edit(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est le professeur de la matière ou un admin
        if (!$this->isGranted('ROLE_ADMIN') && $chapter->getSubject()->getTeacher() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce chapitre.');
        }

        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été modifié avec succès.');
            return $this->redirectToRoute('app_subject_show', ['id' => $chapter->getSubject()->getId()]);
        }

        return $this->render('chapter/edit.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chapter_delete', methods: ['POST'])]
    #[IsGranted('ROLE_TEACHER')]
    public function delete(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est le professeur de la matière ou un admin
        if (!$this->isGranted('ROLE_ADMIN') && $chapter->getSubject()->getTeacher() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce chapitre.');
        }

        if ($this->isCsrfTokenValid('delete'.$chapter->getId(), $request->request->get('_token'))) {
            $subjectId = $chapter->getSubject()->getId();
            $entityManager->remove($chapter);
            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été supprimé avec succès.');
            return $this->redirectToRoute('app_subject_show', ['id' => $subjectId]);
        }

        return $this->redirectToRoute('app_chapter_show', ['id' => $chapter->getId()]);
    }
}
