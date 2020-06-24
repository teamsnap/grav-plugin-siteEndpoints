<?php
namespace Grav\Plugin\Endpoints;

require_once 'resource.php';

use RocketTheme\Toolbox\File\File;
use Symfony\Component\Yaml\Yaml;

/**
 * Version API
 */
class Version extends Resource
{
    /**
     * Get API Version
     *
     * Implements:
     *
     * - GET /api/version
     */
    public function getList()
    {
        $version = $this->getVersion();
        return strtok($version, '.');
    }

    /**
     * Implements:
     *
     * - GET /api/version/major
     * - GET /api/version/minor
     * - GET /api/version/full
     */
    public function getItem()
    {
        switch ($this->getIdentifier()) {
            case 'major':
                // Get API Major Version
                $version = $this->getVersion();
                return strtok($version, '.');
            case 'minor':
                // Get API Minor Version
                $version = $this->getVersion();
                return strtok($version, '.') . '.' . strtok('.');
            case 'full':
                //Get API Full Version
                return $this->getVersion();
        }

        return;
    }

    /**
     * Fetch the plugin version from the blueprints.yaml file
     */
    private function getVersion()
    {
        $locator = $this->grav['locator'];
        $path = $locator->findResource('user://plugins/endpoints', true);
        $fullFileName = $path . DS . 'blueprints.yaml';

        $file = File::instance($fullFileName);
        $data = Yaml::parse($file->content());

        return $data['version'];
    }
}
