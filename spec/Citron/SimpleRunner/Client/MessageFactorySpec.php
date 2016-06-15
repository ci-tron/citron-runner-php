<?php

/**
 * This file is a part of a nekland library
 *
 * (c) Nekland <nekland.fr@gmail.fr>
 *
 * For the full license, take a look to the LICENSE file
 * on the root directory of this project
 */

namespace spec\Citron\SimpleRunner\Client;

use Citron\SimpleRunner\Client\Message;
use Citron\SimpleRunner\Client\MessageFactory;
use PhpSpec\ObjectBehavior;

class MessageFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MessageFactory::class);
    }

    function it_should_throw_error_when_action_format_is_invalid()
    {
        $this::shouldThrow('Citron\SimpleRunner\Exception\InvalidMessageException')->duringCreateMessage('RUNNER:');
    }
    
    function it_should_throw_error_when_json_format_is_wrong()
    {
        $this::shouldThrow('Citron\SimpleRunner\Exception\InvalidMessageException')->duringCreateMessage('good_action:im not json');
    }
    
    function it_should_build_mesage_object_with_correct_data()
    {
        $this::createMessage('action:{"name":"yolo"}')->shouldReturnMessageThat('action', ['name' => 'yolo']);
    }
    
    function it_should_have_null_content_when_no_content()
    {
        $this::createMessage('action:')->shouldReturnMessageThat('action', null);
    }

    public function getMatchers()
    {
        return [
            'returnMessageThat' => function ($subject, $action, $content) {
                if (!$subject instanceof Message) {
                    return false;
                }

                if ($subject->getContent() !== $content) {
                    return false;
                }

                if ($subject->getAction() !== $action) {
                    return false;
                }

                return true;
            }
        ];
    }
}
