<?php

/*
 * This file is part of the oziks/XHProfServiceProvider.
 *
 * (c) Morgan Brunot <brunot.morgan@gmail.com>
 */

namespace Oziks\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;

use Oziks\DataCollector\XHProfDataCollector;
use Oziks\Lib\XHProfRun;

use Exception;

/**
 * XHProfServiceProvider.
 *
 * @author Morgan Brunot <brunot.morgan@gmail.com>
 */
class XHProfServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        if (!extension_loaded('xhprof')) {
            throw new Exception("XHProf extension is not loaded.", 1);
        }

        if (!isset($app['xhprof.location'])) {
            throw new Exception("xhprof.location is required.", 1);
        }

        if (!isset($app['xhprof.host'])) {
            throw new Exception("xhprof.host is required.", 1);
        }

        $app['xhprof'] = $app->share(function() use ($app) {
            return new XHProfRun(
                $app['xhprof.location'],
                $app['xhprof.host']
            );
        });

        $app['twig.path'] = array_merge($app['twig.path'], array(__DIR__.'/../Resources/views/'));

        $app['data_collector.templates'] = array_merge($app['data_collector.templates'], array(array('xhprof', 'Collector/xhprof.html.twig')));
        $app['data_collectors'] = array_merge($app['data_collectors'], array('xhprof' => $app->share(function ($app) { return new XHProfDataCollector($app['xhprof']); })));
    }

    public function boot(Application $app)
    {
    }
}
