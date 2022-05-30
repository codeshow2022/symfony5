<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotifyController
 */
class NotifyController extends AbstractController
{

    /**
     * @var ChatterInterface
     */
    private $slack;

    public function __construct(
        ChatterInterface $slack
    ) {
        $this->slack = $slack;
    }

    /**
     * @Route("/message")
     */
    public function message()
    {

        $message = (new ChatMessage('New Address Book entry added : '))
            ->transport('slack');

        $this->slack->send($message);


        return new Response("Message sent to slack : " . $message->getSubject());
    }

}
