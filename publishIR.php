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

$nb = 1;
if(isset($argv[2]) && is_numeric($argv[2]))
{
    $nb = (int) $argv[2];
}

for ($i=0; $i < $nb; $i++)
{
    $uuid = new Uuid('1e644804-5d00-4583-9e08-bb82c9a3a9b4');
    if(isset($argv[1]) && $argv[1] === "--generate")
    {
        $uuid = new Uuid();
    }

    $message = new Message('publish');
    $message->addHeaders([
        // routing_key header is automatically created by library
        'standard' => 'EAD',
        'version' => '2002',
        'uuid' => (string) $uuid,
        'vendor' => 'scope',
    ]);
    $message->setAttribute('content_type', 'application/xml');
    $message->setBinary(file_get_contents("ir.xml"));

    echo "PUBLISHING IR ... ";

    $result = $client->publish('mnesys.events.findingAid', $message);

    if($result !== false)
    {
        echo green("PUBLISHED !") . PHP_EOL;
    }
    else
    {
        echo red("FAILED :(") . PHP_EOL;
    }
}
