<?php

namespace Tudorica\GoogleBot\Contracts;

interface CommandContract
{
    /**
     * List of allowed users. For everybody: return ['*'].
     *
     * @return array
     */
    public function allowedUsers(): array;

    /**
     * Command name.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Description of the command.
     *
     * @return string
     */
    public function description(): string;

    /**
     * Where all the magic happens.
     *
     * @param string $command
     *
     * @return string
     */
    public function handle(string $command): string;
}