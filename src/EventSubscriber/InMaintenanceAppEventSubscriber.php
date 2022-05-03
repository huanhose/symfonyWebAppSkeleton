<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

/**
 * Suscriber to event Request to check App Maintenance Mode
 */
class InMaintenanceAppEventSubscriber implements EventSubscriberInterface
{
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;    
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (! $this->isMaintenanceModeApp()) {
            return;
        }

        $response = $this->getMaintenanceModePageResponse();
        $event->setResponse($response);
        $event->stopPropagation();
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }

    private function getMaintenanceModePageResponse(): Response
    {
        //Render page
        $html = $this->twig->render('maintenance_mode.html.twig', []);

        //Send response , 503 with a html page
        return new Response(
            $html,
            Response::HTTP_SERVICE_UNAVAILABLE
        );
    }

    private function isMaintenanceModeApp():bool
    {   
        //Read the "MAINTENANCE_APP" Env variable , must be set to 1
        //@TODO This must be moved to a general App service
        $maintenance_app = $_ENV['MAINTENANCE_APP'];
        return $maintenance_app == '1';
    }    
}
