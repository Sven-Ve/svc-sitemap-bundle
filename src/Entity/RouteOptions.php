<?php

declare(strict_types=1);

/*
 * This file is part of the SvcSitemap bundle.
 *
 * (c) 2025 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Entity;

use Svc\SitemapBundle\Enum\ChangeFreq;

/**
 * entity for sitemap.xml definitions.
 */
final class RouteOptions
{
    public function __construct(
        private string $routeName,
    ) {
    }

    private ?\DateTimeImmutable $lastMod = null;

    private ?float $priority = null;

    private ?ChangeFreq $changeFreq = null;

    private ?string $url = null;

    /**
     * @var array<string, mixed>
     */
    private array $routeParam = [];

    /**
     * @var array<string, string>
     */
    private array $alternates = [];

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getLastMod(): ?\DateTimeImmutable
    {
        return $this->lastMod;
    }

    public function getLastModXMLFormat(): ?string
    {
        if ($this->lastMod) {
            return $this->lastMod->format('c');
        }

        return null;

    }

    public function setLastMod(\DateTimeImmutable $lastMod): self
    {
        $this->lastMod = $lastMod;

        return $this;
    }

    public function getPriority(): ?float
    {
        return $this->priority;
    }

    public function setPriority(float $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getChangeFreq(): ?ChangeFreq
    {
        return $this->changeFreq;
    }

    public function getChangeFreqText(): ?string
    {
        if ($this->getChangeFreq()) {
            return $this->changeFreq->value;
        }

        return null;

    }

    public function setChangeFreq(?ChangeFreq $changeFreq): self
    {
        $this->changeFreq = $changeFreq;

        return $this;
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

    /**
     * @return array<string, string>
     */
    public function getAlternates(): array
    {
        return $this->alternates;
    }

    public function addAlternate(string $lang, string $url): void
    {
        $this->alternates[$lang] = $url;
    }
}
