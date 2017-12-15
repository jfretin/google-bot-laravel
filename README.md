# Laravel Package for Google Chat Bot

This package allows you to handle Google Chat Bot commands triggered through the new Google Chat Web Client. You can 
register multiple bots, each with it's custom commands.  

## Requirements

Laravel 5

## Installation

Install the package using [Composer](https://getcomposer.org/) package manager. Run following command in your project root:

```sh
composer require tudor2004/google-bot-laravel
```

Afterwards create an outgoing webhook on the new Google Chat Web Client for each of the bots registered.

## Laravel 5

If you are allowing package discovery, than you don't need to do anything. The package registers it's own service provider 
and the `GoogleBot` facade. 

Otherwise, add firstly the service provider to the `providers` array in `config/app.php`:

```php
'providers' => [
  Tudorica\GoogleBot\GoogleBotServiceProvider::class,
],
```

Then add the facade to your `aliases` array:

```php
'aliases' => [
  ...
  'GoogleBot' => Tudorica\GoogleBot\Facades\GoogleBotFacade::class,
],
``` 

## Usage

After you've successfully registered your outgoing webhooks create a controller that responds to the webhook calls. The 
Google Chat pushes there events that are then handled by the bots registered.  

Note that if you're using the facade in a namespace (e.g. `App\Http\Controllers` in Laravel 5) you'll need 
to either `use GoogleBot` at the top of your class to import it, or append a backslash to access the root namespace 
directly when calling methods, e.g. `\GoogleBot::run($botClass, $data)`.


```php
// Handle commands for a custom 'audio' bot. We pass all the request data that the Google Chat event sends us.
namespace App\Http\Controllers;

class WebhookController extends Controller
{
    public function webhook(Request $request)
    {
        try {        
            
            return GoogleBot::run(AudioBot::class, $request->all());
            
        } catch(BotException $ex) {
            return [
                'text' => 'Something wrong happend';
            ];
        }             
    }
}
```

### Registering bots

Each bot must implement the BotContract interface, available in this package. This interface demands that you define the 
`name()` and `commands()` methods.

```php
namespace App\Bots;
 
use Tudorica\GoogleBot\Contracts\BotContract;
 
class AudioBot extends BotContract
{
     public function name(): string
     {
         return 'Radio';
     }
 
     public function commands(): array
     {
         return [             
             AudioVolume::class             
         ];
     }
}
```

### Registering commands

As you can see in the previous example, each bot can register multiple commands. Each command has to implement the 
`CommandContract` interface which demands that you define the `allowedUsers()`, `name()`, `description()` and a `handle()` method.

```php
namespace App\Commands;
 
use Tudorica\GoogleBot\Contracts\CommandContract;
 
class AudioVolume implements CommandContract
{
    /**
     * You can here define a list of users that can perform this command. Use [*] to allow everyone.
     */ 
    public function allowedUsers(): array
    {
        return ['*'];
    }

    /**
     * Define here the command name. You can also use here regular expressions.
     */
    public function name(): string
    {
        return '!audio volume (.+)';
    }

    /**
     * This description message will be used for the help.
     */
    public function description(): string
    {
        return 'Set volume.';
    }

    /**
     * The handler the executes the command. This has to respond with a string, that will be in the end be returned in the chat room.
     */
    public function handle(string $command): string
    {
        // Some logic for turning the volume of your speakers up or down...

        return 'Ok, volume is now ' . $volume . '.';
    }
}
```