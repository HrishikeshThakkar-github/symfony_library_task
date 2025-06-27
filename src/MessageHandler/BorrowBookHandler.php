<?php

// src/MessageHandler/BorrowBookHandler.php
namespace App\MessageHandler;

use App\Message\BorrowBook;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Entity\Loan;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class BorrowBookHandler implements MessageHandlerInterface
{
    private BookRepository $bookRepo;
    private UserRepository $userRepo;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(
        BookRepository $bookRepo,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->bookRepo = $bookRepo;
        $this->logger = $logger;
    }

    public function __invoke(BorrowBook $message, MessageBusInterface $messageBus): void
    {
        $book = $this->bookRepo->find($message->getBookId());
        $user = $this->userRepo->find($message->getUserId());

        if (!$book || !$user) {
            $this->logger->warning("Book/User not found for borrowing.");
            return;
        }

        if (!$book->isIsAvailable()) {
            $this->logger->info("Book {$book->getId()} is not available.");
            return;
        }

        $loan = new Loan();
        $loan->setLoanedAt(new \DateTime());
        $loan->setBook($book);
        $loan->setUser($user);

        $book->setIsAvailable(false);

        $this->em->persist($loan);
        $this->em->flush();

        //$this->$messageBus->dispatch(new BookBorrowedEmail($book->getId(), $user->getId()));

        //now this will be a new message jus that it will be executed after the borrow book this is called chaining of messages

    }
}
