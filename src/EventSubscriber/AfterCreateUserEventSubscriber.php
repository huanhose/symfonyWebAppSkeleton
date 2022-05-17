<?php

namespace App\EventSubscriber;

use App\Event\AfterCreateUserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Example of Suscriber to event After create user
 */
class AfterCreateUserEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'user.after_create' => 'onAfterCreateUser',
        ];
    }

    public function onAfterCreateUser(AfterCreateUserEvent $event)
    {
        $user = $event->getUser();

        //put here code to react to event
    }
}