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
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $returendAt;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="loans")
     */
    private $Book;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="loans")
     */
    private $User;

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

        if (!$this->dueAt) {
            $this->dueAt = (clone $loanedAt)->modify('+14 days');
        }

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

    public function getBook(): ?Book
    {
        return $this->Book;
    }

    public function setBook(?Book $Book): self
    {
        $this->Book = $Book;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
}


