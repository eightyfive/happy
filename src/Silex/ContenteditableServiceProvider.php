<?php
namespace Eyf\Happy\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Silex\ServiceControllerResolver;

use Eyf\Happy\Controller\ContenteditableController;
use Eyf\Happy\EventListener\ContenteditableListener;
use Eyf\Happy\EventListener\ContenteditableToolbarListener;
use Eyf\Happy\ContenteditableService;
use Eyf\Happy\Twig\Extension\EditableExtension;

use Eyf\Happy\Twig\Extension\TransEditableExtension;
use Eyf\Happy\Twig\Extension\EntityEditableExtension;
use Eyf\Happy\Editor\TransEditor;

/**
 * Contenteditable Service provider.
 *
 * @author Benoit Sagols <benoit.sagols@gmail.com>
 */
class ContenteditableServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{
    public function register(Application $app)
    {
        $app['contenteditable.prefix'] = '-';
        $app['translator.resources'] = array();

        $app['contenteditable.controller'] = $app->share(function ($app) {
            return new ContenteditableController($app['kernel'], $app['content']);
        });

        $app['contenteditable.listener'] = $app->share(function ($app) {
            return new ContenteditableListener($app['content']);
        });
        $app['contenteditable.toolbar.listener'] = $app->share(function ($app) {
            return new ContenteditableToolbarListener($app['content'], $app['security'], $app['twig']);
        });

        $app['contenteditable.editor.trans'] = $app->share(function($app) {
            $transEditor = new TransEditor($app['translator']);
            foreach ($app['translator.resources'] as $resource) {
                list($format, $filename, $locale, $domain) = $resource;
                $transEditor->addResource($format, $filename, $locale, $domain);
            }

            return $transEditor;
        });

        $app['content'] = $app->share(function($app) {

            $content = new ContenteditableService($app['security'], $app['contenteditable.prefix']);
            $content->addEditor($app['contenteditable.editor.trans']);

            return $content;
        });

        $app->extend('twig', function($twig, $app) {
            $twig->addExtension(new TransEditableExtension($app['content'], $app['translator']));
            $twig->addExtension(new EntityEditableExtension($app['content']));

            return $twig;
        });

        $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem', function ($loader, $app) {
            $loader->addPath($app['contenteditable.templates_path'], 'Contenteditable');

            return $loader;
        }));

        $app['contenteditable.templates_path'] = function () {
            $r = new \ReflectionClass('Eyf\\Happy\\Silex\\ContenteditableServiceProvider');

            return dirname(dirname($r->getFileName())).'/Resources/views';
        };
    }

    public function connect(Application $app)
    {
        if (!$app['resolver'] instanceof ServiceControllerResolver) {
            // using RuntimeException crashes PHP?!
            throw new \LogicException('You must enable the ServiceController service provider to be able to use the Contenteditable service.');
        }

        $controllers = $app['controllers_factory'];

        $controllers->get('/edit/{pathname}', 'contenteditable.controller:editAction')->assert('pathname', '.*')->bind('_content_edit');
        $controllers->post('/save/{pathname}', 'contenteditable.controller:saveAction')->assert('pathname', '.*')->bind('_content_save');

        return $controllers;
    }

    public function boot(Application $app)
    {
        $dispatcher = $app['dispatcher'];
        $dispatcher->addSubscriber($app['contenteditable.listener']);
        $dispatcher->addSubscriber($app['contenteditable.toolbar.listener']);

        $app->mount($app['contenteditable.prefix'], $this->connect($app));
    }
}
