# SvcSitemapBundle

[![CI](https://github.com/Sven-Ve/svc-sitemap-bundle/actions/workflows/php.yml/badge.svg)](https://github.com/Sven-Ve/svc-sitemap-bundle/actions/workflows/php.yml)
[![Latest Stable Version](https://poser.pugx.org/svc/sitemap-bundle/v)](https://packagist.org/packages/svc/sitemap-bundle)
[![License](https://poser.pugx.org/svc/sitemap-bundle/license)](https://packagist.org/packages/svc/sitemap-bundle)
[![Total Downloads](https://poser.pugx.org/svc/sitemap-bundle/downloads)](https://packagist.org/packages/svc/sitemap-bundle)
[![PHP Version Require](http://poser.pugx.org/svc/sitemap-bundle/require/php)](https://packagist.org/packages/svc/sitemap-bundle)


###  This bundle create an easy XML sitemap and robots.txt in a Symfony application

> [!CAUTION] 
> The bundle is still at a very early stage, errors may occur!

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
```

* [Installation](docs/01-installation.md)
* [Configuration](docs/02-config.md)
* [Static routes](docs/03-static_routes.md)
* [Dynamic routes](docs/04-dynamic_routes.md)
* [Dumping sitemap.xml](docs/05-dump_sitemap.md)

> [!NOTE]  
> Many thanks to the creators of the [PrestaSitemapBundle](https://github.com/prestaconcept/PrestaSitemapBundle). 
> From there I got many ideas and sometimes also some code...