<?php

// src/Messenger/Stamp/AuditStamp.php
namespace App\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class AuditStamp implements StampInterface
{
    private string $action;
    private \DateTimeImmutable $timestamp;

    public function __construct(
        string $action,
        \DateTimeImmutable $timestamp
    ) {
        $this->timestamp = $timestamp;
        $this->action = $action;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }
}
