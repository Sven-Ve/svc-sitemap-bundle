# SvcSitemapBundle

[![CI](https://github.com/Sven-Ve/svc-sitemap-bundle/actions/workflows/php.yml/badge.svg)](https://github.com/Sven-Ve/svc-sitemap-bundle/actions/workflows/php.yml)
[![Latest Stable Version](https://poser.pugx.org/svc/sitemap-bundle/v)](https://packagist.org/packages/svc/sitemap-bundle)
[![License](https://poser.pugx.org/svc/sitemap-bundle/license)](https://packagist.org/packages/svc/sitemap-bundle)
[![Total Downloads](https://poser.pugx.org/svc/sitemap-bundle/downloads)](https://packagist.org/packages/svc/sitemap-bundle)
[![PHP Version Require](http://poser.pugx.org/svc/sitemap-bundle/require/php)](https://packagist.org/packages/svc/sitemap-bundle)
[![Symfony](https://img.shields.io/badge/symfony-6+%20|%207+%20|%208+-green)](https://symfony.com/)
![Last commit](https://img.shields.io/github/last-commit/Sven-Ve/svc-sitemap-bundle)

###  This bundle creates easy XML sitemaps and robots.txt files in a Symfony application

## Features

- ✅ **XML Sitemap Generation** with full support for lastmod, changefreq, and priority
- ✅ **PHP Attributes Support** - Modern `#[Sitemap]` and `#[Robots]` attributes for type-safe configuration (PHP 8+)
- ✅ **Multi-language Support** with hreflang alternate URLs
- ✅ **robots.txt Generation** with optional sitemap reference
- ✅ **Static & Dynamic Routes** via event system
- ✅ **Security Validation** - Prevents XSS (javascript:, data: schemes blocked)
- ✅ **UTF-8 Validation** - Ensures all content is valid UTF-8
- ✅ **Size Limits** - Automatic validation (50,000 URLs / 50MB per sitemap)
- ✅ **Console Commands** for easy generation
- ✅ **GZIP Support** for compressed sitemaps

### Short examples

#### sitemap.xml
```xml
<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns:xhtml="http://www.w3.org/1999/xhtml">
  <url>
    <loc>https://shorter.li/svc-contactform/de/contact/</loc>
    <lastmod>2024-12-09T15:07:58+01:00</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.2</priority>
    <xhtml:link rel="alternate" hreflang="de" href="https://shorter.li/svc-contactform/de/contact/"/>
    <xhtml:link rel="alternate" hreflang="en" href="https://shorter.li/svc-contactform/en/contact/"/>
  </url>
  <url>
    <loc>https://shorter.li/de/</loc>
    <lastmod>2024-12-09T15:07:58+01:00</lastmod>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
    <xhtml:link rel="alternate" hreflang="de" href="https://shorter.li/de/"/>
    <xhtml:link rel="alternate" hreflang="en" href="https://shorter.li/en/"/>
  </url>
  <url>
    <loc>https://shorter.li/login/de</loc>
    <lastmod>2024-12-09T15:07:58+01:00</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.1</priority>
    <xhtml:link rel="alternate" hreflang="de" href="https://shorter.li/login/de"/>
    <xhtml:link rel="alternate" hreflang="en" href="https://shorter.li/login/en"/>
  </url>
</urlset>
```

#### robots.txt
```
User-agent: google
Allow: /
Allow: /de/
Allow: /en/
Allow: /public
Disallow: /admin

User-agent: bing
Allow: /
Disallow: /de/
Disallow: /en/
Disallow: /admin

User-agent: *
Disallow: /

Sitemap: https://example.com/sitemap.xml
```

## Documentation

### Sitemap
* [Installation](docs/01-installation.md)
* [Configuration](docs/02-config.md)
* [Static sitemap routes](docs/03-static_routes.md)
* [Dynamic sitemap routes](docs/04-dynamic_routes.md)
* [Dumping sitemap.xml](docs/05-dump_sitemap.md)

### Robots.txt
* [Static robots.txt configuration](docs/06-robots_static.md)
* [Dynamic robots.txt rules](docs/07-robots_dynamic.md)
* [Generating robots.txt](docs/08-dump_robots.md)

> [!NOTE]  
> Many thanks to the creators of the [PrestaSitemapBundle](https://github.com/prestaconcept/PrestaSitemapBundle). 
> From there I got many ideas and sometimes also some code...