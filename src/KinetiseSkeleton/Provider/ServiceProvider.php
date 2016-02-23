<?php

namespace KinetiseSkeleton\Provider;

use KinetiseSkeleton\Controller\Api\CommentsController;
use KinetiseSkeleton\Controller\Api\SampleController;
use KinetiseSkeleton\Controller\Tutorial\IndexController;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['controllers.api.sample'] = $app->share(function() use ($app) {
            return new SampleController($app);
        });

        $app['controllers.tutorial.index'] = $app->share(function() use ($app) {
            return new IndexController($app);
        });

        $app['controllers.welcome.index'] = $app->share(function() use ($app) {
            return new \KinetiseSkeleton\Controller\Welcome\IndexController($app);
        });
    }

    public function boot(Application $app)
    {
        /** @var ControllerCollection $api */
        $api = $app['controllers_factory'];
        $app->mount('/api', $api);

        $api->before(function(Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);

                $request->request->set(
                    '_json',
                    $data ? $data : array()
                );
            }
        });

        $api->post('/sample/get-hook', 'controllers.api.sample:getAction')->bind('api_sample_getHook');

        /** @var ControllerCollection $tutorial */
        $tutorial = $app['controllers_factory'];
        $app->mount('/tutorial', $tutorial);

        $tutorial
            ->get('/{section}', 'controllers.tutorial.index:indexAction')
            ->value('section', 'index')
            ->bind('tutorial_index');

        /** @var ControllerCollection $welcome */
        $welcome = $app['controllers_factory'];
        $app->mount('/', $welcome);

        $welcome->get('/', 'controllers.welcome.index:indexAction')->bind('welcome_index');
    }

}