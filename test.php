<?php

require 'vendor/autoload.php';

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
    'uuid' => (string) new Uuid(),
    'vendor' => 'scope',
]);
$message->setAttribute('content_type', 'application/xml');
$message->setBinary(file_get_contents("test.xml"));

echo "PUBLISHING ..." . PHP_EOL;

$result = $client->publish('mnesys.events.findingAid', $message);

if($result !== false)
{
    echo "PUBLISHED !" . PHP_EOL;
}
else
{
    echo "PUBLISH FAILED" . PHP_EOL;
}
