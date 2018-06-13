<?php

require 'vendor/autoload.php';
require __DIR__ . '/commons/bashColors.php';

use Puzzle\Configuration\Memory;
use Puzzle\AMQP\Clients\Pecl;
use Puzzle\AMQP\Messages\Message;
use Puzzle\ValueObjects\Uuid;

$configuration = new Memory(array(
    'amqp/broker/host' => 'rabbitmq',
    'amqp/broker/login' => 'guest',
    'amqp/broker/password' => 'guest',
    'amqp/broker/vhost' => '/',
    'app/id' => 'test-php',
));

$client = new Pecl($configuration);

if(! isset($argv[1]))
{
    throw new \InvalidArgumentException('Missing media path parameter');
}

$filepath = $argv[1];
$extension = pathinfo($filepath, PATHINFO_EXTENSION);

$uuid = new Uuid('1e644804-5d00-4583-9e08-bb82c9a3a9b4');
if(isset($argv[2]) && $argv[2] === "--generate")
{
    $uuid = new Uuid();
}

$message = new Message('publish.raw');
$message->addHeaders([
    // routing_key header is automatically created by library
    'vendor' => 'scope',
    'media' => [
        'uuid' => (string) $uuid,
        'extension' => $extension
    ],
]);
$message->setAttribute('content_type', mime_content_type($filepath));
$message->setBinary(file_get_contents($filepath));

echo "PUBLISHING Media ... ";

$result = $client->publish('mnesys.events.media', $message);

if($result !== false)
{
    echo green("PUBLISHED !") . PHP_EOL;
}
else
{
    echo red("FAILED :(") . PHP_EOL;
}
