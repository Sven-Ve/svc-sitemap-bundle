# Usage

### Defaults

```yaml
# /config/packages/svc_sitemap.yaml
svc_sitemap:

    # define the default values for the sitemap file
    default_values:

        # Standard change frequency, if not specified in the route
        change_freq:          !php/enum Svc\SitemapBundle\Enum\ChangeFreq::WEEKLY # One of Svc\SitemapBundle\Enum\ChangeFreq::ALWAYS; Svc\SitemapBundle\Enum\ChangeFreq::HOURLY; Svc\SitemapBundle\Enum\ChangeFreq::DAILY; Svc\SitemapBundle\Enum\ChangeFreq::WEEKLY; Svc\SitemapBundle\Enum\ChangeFreq::MONTHLY; Svc\SitemapBundle\Enum\ChangeFreq::YEARLY; Svc\SitemapBundle\Enum\ChangeFreq::NEVER

        # Standard priority (between 0 and 1)
        priority:             0.5

    # The directory in which the sitemap will be created.
    sitemap_directory:    '%kernel.project_dir%/public'

    # Filename of the sitemap file.
    sitemap_filename:     sitemap.xml
```

## URL translation
```yaml
# /config/packages/svc_sitemap.yaml
svc_sitemap:
    # Shoud alternate/translated urls used?
    translation:
        enabled:              false

        # set the default language for translated urls
        default_locale:       en

        # List of supported locales
        # Example:
        # locales: 'en', 'de'
        locales:              []
```

## Configuring your application base url

If you are going to use sitemap console command to create sitemap files you have to set the base URL of where you sitemap files will be accessible. The hostname
of the URL will also be used to make Router generate URLs with hostname.

```yaml
# config/packages/routing.yaml
framework:
    router:
        default_uri: 'https://your-domain.com'
```

> **Note:** You may have noticed that there is nothing specific to this bundle. 
> In fact, doing this you just allowed your whole application to generate URLs from the command line.
> Please have a look to Symfony's [official documentation](https://symfony.com/doc/current/routing.html#generating-urls-in-commands) for more information.

## Exclude sitemap xml from git

I recommend that you do not check in the sitemap.xml file. Depending on the release strategy, this file may otherwise be released from the test system into production...

```git
#.gitignore
/public/sitemap.xml
```