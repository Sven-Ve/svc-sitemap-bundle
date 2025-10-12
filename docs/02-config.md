# Configuration

This page explains all available configuration options for the SvcSitemapBundle.

## Complete Configuration Reference

Here's the complete configuration with all available options and their default values:

```yaml
# /config/packages/svc_sitemap.yaml
svc_sitemap:
  # Sitemap definition
  sitemap:
    # define the default values for the sitemap file
    default_values:

      # Standard change frequency, if not specified in the route
      change_freq:          !php/enum Svc\SitemapBundle\Enum\ChangeFreq::WEEKLY # One of Svc\SitemapBundle\Enum\ChangeFreq::ALWAYS; Svc\SitemapBundle\Enum\ChangeFreq::HOURLY; Svc\SitemapBundle\Enum\ChangeFreq::DAILY; Svc\SitemapBundle\Enum\ChangeFreq::WEEKLY; Svc\SitemapBundle\Enum\ChangeFreq::MONTHLY; Svc\SitemapBundle\Enum\ChangeFreq::YEARLY; Svc\SitemapBundle\Enum\ChangeFreq::NEVER

      # Standard priority (between 0 and 1, as float)
      priority:             0.5

    # The directory in which the sitemap will be created.
    sitemap_directory:    '%kernel.project_dir%/public'

    # Filename of the sitemap file.
    sitemap_filename:     sitemap.xml

  # Robots.txt definition
  robots:
    # The directory in which the robots.txt will be created.
    robots_directory:     '%kernel.project_dir%/public'

    # Filename of the robots.txt file.
    robots_filename:      robots.txt

    # Optional: Full URL to sitemap.xml to include in robots.txt
    # If set, adds a "Sitemap: <url>" line at the end of robots.txt
    sitemap_url:          null  # e.g., 'https://example.com/sitemap.xml'
```

> **Note:** The sitemap automatically validates:
> - Maximum 50,000 URLs per sitemap
> - Maximum 50 MB uncompressed size
> - Valid UTF-8 encoding for all content
> - Secure URL schemes (only http/https allowed)
>
> If these limits are exceeded, a `SitemapTooLargeException` is thrown.

## Sitemap Translation (Multi-language Support)
Enable translation support to generate hreflang alternate URLs for multi-language sites:

```yaml
# /config/packages/svc_sitemap.yaml
svc_sitemap:
  sitemap:
    translation:
      # Enable alternate/translated URLs
      enabled: true

      # Set the default language for translated URLs
      default_locale: en

      # List of supported locales
      locales:
        - en
        - de
        - fr
```

When enabled, routes with `{_locale}` placeholder will generate alternate URLs with hreflang attributes:

```xml
<url>
  <loc>https://example.com/en/about</loc>
  <xhtml:link rel="alternate" hreflang="en" href="https://example.com/en/about"/>
  <xhtml:link rel="alternate" hreflang="de" href="https://example.com/de/about"/>
  <xhtml:link rel="alternate" hreflang="fr" href="https://example.com/fr/about"/>
</url>
```

## Router Configuration (Required for Console Commands)

To generate sitemaps via console commands, you must configure the router's `default_uri`:

```yaml
# config/packages/routing.yaml
framework:
    router:
        default_uri: 'https://your-domain.com'
```

This allows the router to generate absolute URLs from the command line. See Symfony's [official documentation](https://symfony.com/doc/current/routing.html#generating-urls-in-commands) for more information.

## Robots.txt Translation

Similar to sitemap translation, you can enable translation for robots.txt rules:

```yaml
# /config/packages/svc_sitemap.yaml
svc_sitemap:
  robots:
    translation:
      enabled: true
```

When enabled, routes with `{_locale}` placeholder in robots.txt rules will be expanded for all configured locales.

## Best Practices

### Exclude Generated Files from Git

It's recommended to exclude generated sitemap/robots files from version control:

```gitignore
# .gitignore
/public/sitemap.xml
/public/sitemap.xml.gz
/public/robots.txt
```

This prevents accidentally deploying test/staging content to production.

### Performance Recommendations

1. **Use console commands** to generate static files rather than dynamic controllers
2. **Enable GZIP** for sitemap.xml to reduce file size
3. **Set appropriate change frequencies** - don't use "always" unless content truly changes constantly
4. **Use caching** for dynamic route generation if you have many database-driven URLs

### SEO Best Practices

1. **Priority values:**
   - Homepage: 1.0
   - Main sections: 0.8
   - Sub-pages: 0.5
   - Less important pages: 0.3

2. **Change frequency:**
   - News/blogs: `DAILY` or `HOURLY`
   - Product pages: `WEEKLY`
   - About/static pages: `MONTHLY`

3. **Always reference your sitemap in robots.txt:**
   ```yaml
   svc_sitemap:
     robots:
       sitemap_url: 'https://your-domain.com/sitemap.xml'
   ```

## See Also

- [Static routes](03-static_routes.md) - Configure routes for sitemap
- [Dynamic routes](04-dynamic_routes.md) - Add dynamic content
- [Static robots.txt](06-robots_static.md) - Configure robots.txt rules