<?php
// src/Controller/LoanController.php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Loan;
use App\Event\BookBorrowEvent;
use App\Message\BorrowBook;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoanController extends AbstractController
{
    /**
     * @Route("/borrow/{id}", name="borrow_book")
     */
    public function borrow(Book $book, EntityManagerInterface $em, EventDispatcherInterface $dispatcher, MessageBusInterface $bus): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if (!$book->isIsAvailable()) {
            $this->addFlash('error', 'This book is currently not available.');
            return $this->redirectToRoute('available_books');
        }

        $user = $this->getUser(); // Logged-in customer
//
//        $loan = new Loan();
//        $event =new BookBorrowEvent($loan,$book,$user);
//        $dispatcher->dispatch($event,BookBorrowEvent::NAME);




        //this is the implementation using messenger so that tasks can be run asynchronously
        $bus->dispatch(new BorrowBook($book->getId(), $user->getId()));


        $this->addFlash('success', 'You have successfully borrowed the book.');

        return $this->redirectToRoute('app_customer_loan');
    }
}

