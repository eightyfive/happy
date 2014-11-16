<?php
namespace Eyf\Happy\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Eyf\Happy\ContenteditableService;

/**
 *
 * @author Benoit Sagols <benoit.sagols@gmail.com>
 */
class ContenteditableListener implements EventSubscriberInterface
{
    protected $content;

    public function __construct(ContenteditableService $content)
    {
        $this->content = $content;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->attributes->has('_contenteditable')) {
            $this->content->edit($request->getPathInfo(), $request->attributes->get('_route'), $request->attributes->get('_route_params'));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
        );
    }
}
