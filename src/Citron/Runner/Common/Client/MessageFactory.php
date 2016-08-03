<?php
/**
 * This file is a part of a nekland library
 *
 * (c) Nekland <nekland.fr@gmail.fr>
 *
 * For the full license, take a look to the LICENSE file
 * on the root directory of this project
 */

namespace Citron\Runner\Common\Client;


use Citron\Runner\Common\Exception\InvalidMessageException;

class MessageFactory
{
    public static function createMessage($message)
    {
        if (!preg_match('/^([a-z\_]+):(.*)/', $message, $matches)) {
            throw new InvalidMessageException;
        }

        $message = new Message();

        $content = json_decode($matches[2], true);
        if ($matches[2] !== 'null' && $matches[2] !== '' && $content === null) {
            throw new InvalidMessageException('The json content is invalid');
        }

        $message->setContent($content);
        $message->setAction($matches[1]);
        
        return $message;
    }
}
