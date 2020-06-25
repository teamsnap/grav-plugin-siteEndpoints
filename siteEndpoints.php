<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Grav;
use Grav\Common\Plugin;

class SiteEndpointsPlugin extends Plugin
{
    protected $route = 'api';

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => [
                ['autoload', 100000],
            ],
            'onPagesInitialized' => ['onPagesInitialized', 0],
        ];
    }

    /**
     * [onPluginsInitialized:100000] Composer autoload.
     *
     * @return ClassLoader
     */
    public function autoload()
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    public function onPagesInitialized()
    {
        $uri = $this->grav['uri'];
        $config = $this->config->get('plugins.siteEndpoints');

        if (strpos($uri->path(), $this->route) === false) {
            return;
        }

        $paths = $this->grav['uri']->paths();
        $paths = array_splice($paths, 1);
        $resource = $paths[0];

        if ($resource) {
            $file = __DIR__ . '/resources/' . $resource . '.php';
            if (file_exists($file)) {
                require_once $file;
                $resourceClassName = '\Grav\Plugin\SiteEndpoints\\' . ucfirst($resource);
                $resource = new $resourceClassName($this->grav);
                $output = $resource->execute($config);
                $resource->setHeaders();

                echo $output;
            } else {
                header('HTTP/1.1 404 Not Found');
            }
        }

        exit();
    }
}
