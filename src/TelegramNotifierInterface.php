<?php

namespace newbie67\TelegramNotifier;

use Exception;

/**
 * Interface TelegramNotifierInterface
 *
 * @package newbie67\TelegramNotifier
 */
interface TelegramNotifierInterface
{
    /**
     * @param string $message
     *
     * @return bool return false if it can't send message even for one of recipients
     */
    public function send(string $message): bool;
}
