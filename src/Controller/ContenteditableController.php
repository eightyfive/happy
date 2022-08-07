<?php
namespace Eyf\Happy\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Happy\ContenteditableService;

class ContenteditableController
{
    protected $kernel;
    protected $content;

    public function __construct(HttpKernelInterface $kernel, ContenteditableService $content)
    {
        $this->kernel = $kernel;
        $this->content = $content;
    }

    public function editAction(Request $request, $pathname)
    {
        $pathInfo = '/'.$pathname;

        $subRequest = Request::create($pathInfo, 'GET', array(), array(), array(), array('HTTP_HOST'=>$request->getHttpHost()));
        $subRequest->attributes->set('_contenteditable', true);

        $response = $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);

        return $response;
    }

    public function saveAction(Request $request)
    {
        $editions = $request->request->all();

        foreach ($editions as $keyEditor => $data) {
            $this->content->save($keyEditor, $data);
        }

        return new JsonResponse();
    }
}

