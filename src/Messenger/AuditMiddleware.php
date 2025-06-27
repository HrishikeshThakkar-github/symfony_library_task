<?php
// src/Messenger/AuditMiddleware.php
namespace App\Messenger;

use App\Messenger\Stamp\AuditStamp;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class AuditMiddleware implements MiddlewareInterface
{
    private $logger;
    public function __construct(LoggerInterface $messengerAuditLogger){
        $this->logger = $messengerAuditLogger;
    }
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        // pre-processing
        dump('Before middleware chain');

        $envelope = $stack->next()->handle($envelope, $stack);
        $stamps = $envelope->all(AuditStamp::class);

        if ($stamps) {
            $auditStamp = $stamps[0];
            $action = $auditStamp->getAction();
            $timestamp = $auditStamp->getTimestamp();
            dump($action, $stamps, $timestamp);
        }
        // post-processing
        dump('After middleware chain');

        return $envelope;
    }
}
