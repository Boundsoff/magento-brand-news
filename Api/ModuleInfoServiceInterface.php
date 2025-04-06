<?php

namespace Boundsoff\BrandNews\Api;

interface ModuleInfoServiceInterface
{
    /**
     * List of modules and their version with information about update
     *
     * @return array{name: string, version: string, version_available: string}
     */
    public function getVersions(): array;
}
