<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register/{type}", name="app_register")
     */
    public function register(
        string $type,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if (!in_array($type, ['admin', 'customer'])) {
            throw $this->createNotFoundException('Invalid user type');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'user_type' => $type,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($hashedPassword);
            $user->setRoles([$type === 'admin' ? 'ROLE_ADMIN' : 'ROLE_CUSTOMER']);

            $entityManager->persist($user);
            $entityManager->flush();

            //return $this->redirectToRoute($type === 'admin' ? 'admin_dashboard' : 'customer_dashboard');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'type' => $type,
        ]);
    }

    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function adminDashboard(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/customer/dashboard", name="customer_dashboard")
     */
    public function customerDashboard(): Response
    {
        return $this->render('Dashboard/customer.html.twig');
    }
}
