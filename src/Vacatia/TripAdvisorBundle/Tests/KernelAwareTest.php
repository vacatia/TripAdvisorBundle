<?php

namespace Vacatia\TripAdvisorBundle\Tests;

// the things we need are waaaay up
$dir = __DIR__;

while (!file_exists($dir . '/app')) {
    $dir = dirname($dir);
}

// require everthing needed
require_once $dir . '/vendor/autoload.php';
require_once $dir . '/app/bootstrap.php.cache';
require_once $dir . '/app/AppKernel.php';
if (file_exists($dir . '/app/env.php')) {
    require_once $dir . '/app/env.php';
} else {
    require_once $dir . '/app/env.php';
}

abstract class KernelAwareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\HttpKernel\\AppKernel
     */
    protected $kernel;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @return null
     */
    public function setUp()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');

        $this->kernel = new \AppKernel('dev', true);
        $this->kernel->loadClassCache();

        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();

        parent::setUp();
    }

    /**
     * @return null
     */
    public function tearDown()
    {
        $this->kernel->shutdown();

        parent::tearDown();
    }
}
