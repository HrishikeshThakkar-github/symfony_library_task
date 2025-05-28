<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoanRepository::class)
 */
class Loan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $loanedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dueAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $returendAt;

    /**
     * @ORM\ManyToOne(targetEntity=book::class, inversedBy="loans")
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="loans")
     */
    private $user;

    /**
     * @ORM\PrePersist
     */
    public function setDueDate(): void
    {
        if (!$this->dueAt && $this->loanedAt) {
            $this->dueAt = (clone $this->loanedAt)->modify('+14 days');
        }
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLoanedAt(): ?\DateTimeInterface
    {
        return $this->loanedAt;
    }

    public function setLoanedAt(\DateTimeInterface $loanedAt): self
    {
        $this->loanedAt = $loanedAt;

        return $this;
    }

    public function getDueAt(): ?\DateTimeInterface
    {
        return $this->dueAt;
    }

    public function setDueAt(\DateTimeInterface $dueAt): self
    {
        $this->dueAt = $dueAt;

        return $this;
    }

    public function getReturendAt(): ?\DateTimeInterface
    {
        return $this->returendAt;
    }

    public function setReturendAt(\DateTimeInterface $returendAt): self
    {
        $this->returendAt = $returendAt;

        return $this;
    }

    public function getBook(): ?book
    {
        return $this->book;
    }

    public function setBook(?book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }
}
