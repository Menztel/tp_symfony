<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BanController extends AbstractController
{
    #[Route('/banned', name: 'app_banned')]
    public function banned(): Response
    {
        return $this->render('ban/banned.html.twig');
    }
}
