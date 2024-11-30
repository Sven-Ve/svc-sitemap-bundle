<?php

namespace Svc\SitemapBundle\Entity;

use Svc\SitemapBundle\Enum\ChangeFreq;

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
    } else {
      return null;
    }
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
    } else {
      return null;
    }
  }

  public function setChangeFreq(?ChangeFreq $changeFreq): self
  {
    $this->changeFreq = $changeFreq;

    return $this;
  }
}
