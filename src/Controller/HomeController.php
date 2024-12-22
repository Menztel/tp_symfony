<?php

namespace App\Controller;

use App\Repository\SubjectRepository;
use App\Repository\ChapterRepository;
use App\Repository\ExerciseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        SubjectRepository $subjectRepository,
        ChapterRepository $chapterRepository,
        ExerciseRepository $exerciseRepository
    ): Response {
        return $this->render('home/index.html.twig', [
            'subjects' => $subjectRepository->findBy([], ['id' => 'DESC']),
            'chapters' => $chapterRepository->findAll(),
            'exercises' => $exerciseRepository->findBy([], ['id' => 'DESC']),
        ]);
    }
}
