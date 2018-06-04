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

$message = new Message('publish');
$message->addHeaders([
    // routing_key header is automatically created by library
    'standard' => 'EAD',
    'version' => '2002',
    'uuid' => (string) new Uuid('f1ee3729-e94b-48bd-a0fd-8e06ba348c88'),
    'vendor' => 'scope',
]);
$message->setAttribute('content_type', 'application/xml');
$message->setBinary(file_get_contents("cdc.xml"));

echo "PUBLISHING CDC ..." . PHP_EOL;

$result = $client->publish('mnesys.events.classificationScheme', $message);

if($result !== false)
{
    echo green("PUBLISHED !" . PHP_EOL);
}
else
{
    echo red("PUBLISH FAILED") . PHP_EOL;
}
