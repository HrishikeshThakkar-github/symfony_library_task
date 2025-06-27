<?php

namespace App\EventListener;

use App\Event\BookBorrowEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class BookBorrowEventListener implements EventSubscriberInterface {


    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var MailerInterface
     */
    public $mailer;

    public function __construct(EntityManagerInterface $em,  MailerInterface $mailer) {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onBookBorrow(BookBorrowEvent $event) {


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

        $email = (new TemplatedEmail())
            ->from('hrishikeshthakkar.19@gmail.com')
            ->to($user->getEmail())
            ->htmlTemplate('emails/book_borrow.html.twig')
            ->context([
                'user' => $user,
                'loan' => $loan,
                'book' => $book,
            ]);

        $this->mailer->send($email);




    }

    public static function getSubscribedEvents()
    {
        return [
            BookBorrowEvent::NAME => 'onBookBorrow',
        ];

    }
}