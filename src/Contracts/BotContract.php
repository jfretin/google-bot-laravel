<?php

namespace Tudorica\GoogleBot\Contracts;

interface BotContract
{
    /**
     * The bot name.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Command that the current bot registers.
     *
     * @return array
     */
    public function commands(): array;
}