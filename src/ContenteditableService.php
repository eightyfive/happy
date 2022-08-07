<?php
namespace Eyf\Happy;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Eyf\Happy\Editor\EditorInterface;

class ContenteditableService
{
    protected $editors = array();

    protected $security;
    protected $prefix;
    protected $disabled = false;
    protected $pathInfo;
    protected $routeName;
    protected $routeParams = array();

    public function __construct(SecurityContextInterface $security, $prefix)
    {
        $this->security = $security;
        $this->prefix = $prefix;
    }

    public function edit($pathInfo, $routeName, $routeParams = array())
    {
        $this->pathInfo = $pathInfo;
        $this->routeName = $routeName;
        $this->routeParams = $routeParams;
    }

    public function isEditable()
    {
        return isset($this->pathInfo) && isset($this->routeName);
    }

    public function getPathInfo()
    {
        return $this->pathInfo;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getPathname()
    {
        return ltrim('/', $this->pathInfo);
    }

    public function getRouteName()
    {
        return $this->routeName;
    }

    public function getRouteParams()
    {
        return $this->routeParams;
    }
    
    public function isGranted()
    {
        return $this->isAuthenticated() && $this->security->isGranted('ROLE_ADMIN') !== false;
    }

    public function isAuthenticated()
    {
        return $this->security->getToken() && $this->security->getToken()->isAuthenticated();
    }

    public function addEditor(EditorInterface $editor)
    {
        $this->editors[$editor->getKey()] = $editor;
    }

    public function save($editorKey, $data)
    {
        if (!isset($this->editors[$editorKey])) {
            throw new \RuntimeException('No editor is registered for key: '.$editorKey);
        }

        $this->editors[$editorKey]->save($data);
    }
}