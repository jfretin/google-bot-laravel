<?php

namespace Tudorica\GoogleBot\Factories;

use Tudorica\GoogleBot\Contracts\BotContract;
use Tudorica\GoogleBot\Exceptions\BotFactoryException;

class BotFactory
{
    /**
     * Create bot by bot class.
     *
     * @param string $botClass
     *
     * @return BotContract
     *
     * @throws BotFactoryException
     */
    public function make(string $botClass): BotContract
    {
        try {
            $bot = app($botClass);

            if (!$bot instanceof BotContract) {
                throw new BotFactoryException('Invalid bot.');
            }

            return $bot;
        } catch (\Throwable $ex) {
            throw new BotFactoryException('Bot does not exists.');
        }
    }
}