<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LoanExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('days_left', [$this, 'calculateDaysLeft']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('days_left', [$this, 'calculateDaysLeft']),
        ];
    }

    public function calculateDaysLeft(\DateTimeInterface $dueAt): string
    {
        $now = new \DateTime();
        $diff = $now->diff($dueAt);

        if ($dueAt < $now) {
            return 'Overdue by '.$diff->days ;
        }
        return $diff->days . ' days left';
    }
}
