<?php

declare(strict_types=1);

namespace App\Asset;

use Carbon\Carbon;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class CustomVersionStrategy implements VersionStrategyInterface
{
    private readonly int $version;

    public function __construct()
    {
        $this->version = (new Carbon('now'))->timestamp;
    }

    public function getVersion(string $path): string
    {
        return (string)$this->version;
    }

    /**
     * Applies version to the supplied path.
     */
    public function applyVersion(string $path): string
    {
        return $path . '?v=' . $this->getVersion($path);
    }
}
