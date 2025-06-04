<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class CustomerLoanController extends AbstractController
{

    /**
     * @Route("/customer/loan", name="app_customer_loan")
     */
    public function index(LoanRepository $loanRepository): Response
    {
        $user = $this->getUser();
        $loans = $loanRepository->findBy(['User' => $user]);

        return $this->render('customer_loan/index.html.twig', [
            'loans' => $loans,
        ]);
    }

    /**
     * @Route("/customer/return/{id}", name="customer_return_book")
     */
    public function returnBook(loan $loan, EntityManagerInterface $em): Response
    {
        if (!$loan->getReturendAt()) {
            $loan->setReturendAt(new \DateTime());
            $loan->getBook()->setIsAvailable(true);
            $em->flush();
            $this->addFlash('success', 'Book returned successfully.');
        } else {
            $this->addFlash('info', 'This book was already returned.');
        }

        return $this->redirectToRoute('app_customer_loan');
    }
}

