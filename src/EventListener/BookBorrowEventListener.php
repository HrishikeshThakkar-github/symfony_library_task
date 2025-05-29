<?php

namespace App\EventListener;

use App\Event\BookBorrowEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BookBorrowEventListener implements EventSubscriberInterface {


    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    public function onBookBorrow(BookBorrowEvent $event){


        $loan = $event->getLoan();
        $book = $event->getBook();
        $user = $event->getUser();
        $loan->setLoanedAt(new \DateTime());
        $loan->setBook($book);
        $loan->setUser($user);

        $book->setIsAvailable(false); // mark book as unavailable
        //now I want to implement it using listener

        $this->em->persist($loan);
        $this->em->flush();




    }

    public static function getSubscribedEvents()
    {
        return [
            BookBorrowEvent::NAME => 'onBookBorrow',
        ];

    }
}