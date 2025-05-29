<?php
namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendOverdueWarningsCommand extends Command
{
    protected static $defaultName = 'app:send-overdue-warnings';

    private $userRepository;
    private $mailer;

    public function __construct(UserRepository $userRepository, MailerInterface $mailer)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this->setDescription('more than 2 overdue books for over 25 days.');
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $today = new \DateTime();

        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $loans = $user->getLoans();
            $book = $loans[0]->getBook();
            //for sending it in throough mail
            if (count($loans) <= 2) {
                continue;
            }

            $overdueCount = 0;

            foreach ($loans as $loan) {
                if ($loan->getReturendAt() !== null) {
                    continue;
                }

                $dueAt = $loan->getDueAt();
                if ($dueAt < $today && $dueAt->diff($today)->days > 25) {
                    $overdueCount++;
                }
            }

            if ($overdueCount >= 2) {
                $email = (new TemplatedEmail())
                    ->from('hrishikeshthakkar.19@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Library Book Overdue Warning')
                    ->htmlTemplate('emails/SendOverdueWarning.html.twig')
                    ->context([
                        'user' => $user,
                        'loan' => $loans,
                        'book' => $book,
                    ]);
                $this->mailer->send($email);
            }
        }

        return Command::SUCCESS;
    }
}
