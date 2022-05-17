<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Twig\Environment;

/**
 * Suscriber to event Request to check App Maintenance Mode
 */
class InMaintenanceAppEventSubscriber implements EventSubscriberInterface
{
    public function __construct(Environment $twig, ContainerBagInterface $params)
    {
        $this->twig = $twig;
        $this->params = $params;
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

    /**
     * Check if de app is in maintenance
     * Read config parameter
     *
     * @return boolean
     */
    private function isMaintenanceModeApp(): bool
    {
        //Read the config parameter
        return $this->params->get('app.maintenance');
    }
}
