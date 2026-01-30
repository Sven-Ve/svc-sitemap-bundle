<?php

declare(strict_types=1);

/*
 * This file is part of the SvcSitemap bundle.
 *
 * (c) 2026 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Entity;

/**
 * entity for robots.txt definitions.
 */
final class RobotsOptions
{
    public function __construct(
        private string $routeName,
    ) {
    }

    /**
     * @var array<string, mixed>
     */
    private array $routeParam = [];

    private bool $allow = false;

    private bool $disallow = false;

    /**
     * @var array<string>
     */
    private array $allowList = ['*'];

    /**
     * @var array<string>
     */
    private array $disallowList = ['*'];

    private string $path;

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRouteParam(): array
    {
        return $this->routeParam;
    }

    /**
     * @param array<string, mixed> $routeParam
     */
    public function setRouteParam(array $routeParam): self
    {
        $this->routeParam = $routeParam;

        return $this;
    }

    public function getAllow(): bool
    {
        return $this->allow;
    }

    public function setAllow(bool $allow): self
    {
        $this->allow = $allow;

        return $this;
    }

    public function getDisallow(): bool
    {
        return $this->disallow;
    }

    public function setDisallow(bool $disallow): self
    {
        $this->disallow = $disallow;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getAllowList(): array
    {
        return $this->allowList;
    }

    /**
     * @param array<string> $allowList
     */
    public function setAllowList(array $allowList): self
    {
        $this->allowList = $allowList;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getDisallowList(): array
    {
        return $this->disallowList;
    }

    /**
     * @param array<string> $disallowList
     */
    public function setDisallowList(array $disallowList): self
    {
        $this->disallowList = $disallowList;

        return $this;
    }

    /**
     * Get the value of path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the value of path.
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }
}
