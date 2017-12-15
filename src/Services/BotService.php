<?php

namespace Tudorica\GoogleBot\Services;

use Tudorica\GoogleBot\Contracts\BotContract;
use Tudorica\GoogleBot\Contracts\CommandContract;
use Tudorica\GoogleBot\Exceptions\BotCannotRunCommandException;
use Tudorica\GoogleBot\Exceptions\BotException;
use Tudorica\GoogleBot\Exceptions\BotNoCommandsRegisteredException;
use Tudorica\GoogleBot\Exceptions\BotUnauthorizedUserException;
use Tudorica\GoogleBot\Factories\BotFactory;

class BotService
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var BotContract
     */
    protected $bot;

    /**
     * Run the bot.
     *
     * @param string $botClass
     * @param array  $data
     *
     * @return array
     *
     * @throws BotException
     */
    public function run(string $botClass, array $data): array
    {
        $this->data = $data;

        $this->bot = app(BotFactory::class)->make($botClass);

        if (empty($this->bot->commands())) {
            throw new BotNoCommandsRegisteredException('Bot has no registered commands.');
        }

        if (!$this->botCanRunCommand($this->getCommandName())) {
            throw new BotCannotRunCommandException('Bot cannot run command.');
        }

        if (!$this->userCanRunCommand($this->getCommandName(), $this->getUserName())) {
            throw new BotUnauthorizedUserException('User not authorized');
        }

        /** @var CommandContract $command */
        $command = $this->getBotCommand($this->getCommandName());

        $response = $command->handle($this->getCommandName());

        return $this->respond($response);
    }

    /**
     * Create chat response.
     *
     * @param string $message
     *
     * @return array
     */
    private function respond(string $message)
    {
        return [
            'text' => $message
        ];
    }

    /**
     * Get the requested command name.
     *
     * @return string
     */
    private function getCommandName(): string
    {
        return isset($this->data['text']) ? $this->data['text'] : '';
    }

    /**
     * Get the username that requested the command.
     *
     * @return string
     */
    private function getUserName(): string
    {
        if (isset($this->data['sender']) && isset($this->data['sender']['name']) && isset($this->data['sender']['type']) && $this->data['sender']['type'] === 'HUMAN') {
            return $this->data['sender']['name'];
        }

        return '';
    }

    /**
     * Check if requested command is supported by bot.
     *
     * @param string $commandName
     *
     * @return bool
     */
    public function botCanRunCommand(string $commandName): bool
    {
        try {
            $this->getBotCommand($commandName);

            return true;
        } catch (BotException $ex) {

        }

        return false;
    }

    /**
     * Check if user is allowed to execute the command.
     *
     * @param string $commandName
     * @param string $userName
     *
     * @return bool
     */
    private function userCanRunCommand(string $commandName, string $userName): bool
    {
        try {
            if (empty($userName)) {
                return false;
            }

            $botCommand = $this->getBotCommand($commandName);

            foreach ($botCommand->allowedUsers() as $user) {
                if ($user === '*' || $user === $userName) {
                    return true;
                }
            }

            return true;
        } catch (BotException $ex) {

        }

        return false;
    }

    /**
     * Get the requested bot command.
     *
     * @param string $commandName
     *
     * @return CommandContract
     *
     * @throws BotException
     */
    private function getBotCommand(string $commandName): CommandContract
    {
        /** @var CommandContract $command */
        foreach ($this->bot->commands() as $botCommand) {
            try {
                $command = app($botCommand);

                if ($command instanceof CommandContract && preg_match('/' . $command->name() . '/', $commandName) === 1) {
                    return $command;
                }
            } catch (\Throwable $ex) {

            }
        }

        throw new BotException('Invalid command');
    }
}