<?php

namespace WebSK\Skif;

use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Entry\LoggerEntryService;

final class Logger
{
    public static function logObjectEvent(
        InterfaceEntity $object,
        string $comment,
        ?string $user_full_id
    ): void {
        $logger = \WebSK\Logger\Logger::self();
        if (!$logger instanceof LoggerEntryService) {
            throw new \UnexpectedValueException('Logger facade did not resolve LoggerEntryService');
        }

        $logger->logObjectEvent($object, $comment, $user_full_id);
    }
}
