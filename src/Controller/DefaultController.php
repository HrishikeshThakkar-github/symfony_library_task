<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findBy(['isAvailable' => true]);
        return $this->render('home.html.twig', [
            'books' => $books,
        ]);
    }
}
