<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register/{type}", name="app_register")
     * @throws TransportExceptionInterface
     */
    public function register(
        string $type,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
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

//            $email=(new Email())
//                ->from('hrishi.pvt@gmail.com')
//                ->to('hrishithakkar2332@gmail.com')
//                ->subject('Test Email')
//                ->text('This is a plain text email.');

            $email = (new TemplatedEmail())
                ->from('hrishikeshthakkar.19@gmail.com')
                ->to($user->getEmail())
                ->htmlTemplate('emails/registration.html.twig')
            ->context([
                'user' => $user,
            ]);

            $mailer->send($email);
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
