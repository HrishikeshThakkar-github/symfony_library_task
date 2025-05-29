<?php
// src/Controller/LoanController.php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Loan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoanController extends AbstractController
{
    /**
     * @Route("/borrow/{id}", name="borrow_book")
     */
    public function borrow(Book $book, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if (!$book->isIsAvailable()) {
            $this->addFlash('error', 'This book is currently not available.');
            return $this->redirectToRoute('available_books');
        }

        $user = $this->getUser(); // Logged-in customer

        $loan = new Loan();
        $loan->setLoanedAt(new \DateTime());
        $loan->setBook($book);
        $loan->setUser($user);

        $book->setIsAvailable(false); // mark book as unavailable

        $em->persist($loan);
        $em->flush();

        $this->addFlash('success', 'You have successfully borrowed the book.');

        return $this->redirectToRoute('app_customer_loan');
    }
}

