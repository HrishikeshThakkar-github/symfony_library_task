<?php

// src/Message/BorrowBook.php
namespace App\Message;

class BorrowBook
{
    private int $bookId;
    private int $userId;

    public function __construct(
        int $bookId,
        int $userId
    ) {
        $this->userId = $userId;
        $this->bookId = $bookId;
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
