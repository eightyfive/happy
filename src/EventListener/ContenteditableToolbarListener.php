<?php
namespace Eyf\Happy\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Happy\ContenteditableService;
/**
 * WebDebugToolbarListener injects the Web Debug Toolbar.
 *
 * The onKernelResponse method must be connected to the kernel.response event.
 *
 * The WDT is only injected on well-formed HTML (with a proper </body> tag).
 * This means that the WDT is never included in sub-requests or ESI requests.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ContenteditableToolbarListener implements EventSubscriberInterface
{
    protected $content;
    protected $security;
    protected $twig;
    protected $role;

    public function __construct(ContenteditableService $content, SecurityContextInterface $security, \Twig_Environment $twig, $role = 'ROLE_ADMIN')
    {
        $this->content = $content;
        $this->security = $security;
        $this->twig = $twig;
        $this->role = $role;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->security->getToken()) {
            return;
        }

        if (!$this->security->isGranted($this->role)) {
            return;
        }

        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()
            || $response->isRedirection()
            || $response->isServerError()
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
        ) {
            return;
        }

        $this->injectToolbar($request, $response);
    }

    /**
     * Injects the web debug toolbar into the given Response.
     *
     * @param Request $request A Response instance
     * @param Response $response A Response instance
     */
    protected function injectToolbar(Request $request, Response $response)
    {
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false !== $pos) {
            $pathname = ltrim($request->getPathInfo(), '/');

            $toolbar = "\n".str_replace("\n", '', $this->twig->render(
                '@Contenteditable/toolbar.html.twig',
                array(
                    'content' => $this->content,
                    'request_pathname' => ltrim($request->getPathInfo(), '/')
                )
            ))."\n";
            $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
            $response->setContent($content);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -128),
        );
    }
}
