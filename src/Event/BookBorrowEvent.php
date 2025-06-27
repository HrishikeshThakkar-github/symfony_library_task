<?php

namespace App\Event;

use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class BookBorrowEvent extends Event
{
    public const NAME = "Book_Borrowed";
    private $Loan;
    private $Book;
    private $user;

    public function __construct(Loan $loan,Book $book,User $user){
        $this->Loan = $loan;
        $this->Book = $book;
        $this->user = $user;
    }

    public function getLoan(): Loan{
        return $this->Loan;
    }

    public function getBook(): Book{
        return $this->Book;
    }

    public function getUser()
    {
        return $this->user;
    }

}