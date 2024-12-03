# Usage

### general

```yaml
# /config/packages/svc_sitemap.yaml
svc_sitemap:
```


## Configuring your application base url

If you are going to use sitemap Dumper to create sitemap files by using CLI command
you have to set the base URL of where you sitemap files will be accessible. The hostname
of the URL will also be used to make Router generate URLs with hostname.

```yaml
# config/packages/routing.yaml
framework:
    router:
        default_uri: 'https://your-domain.com'
```

> **Note:** You may have noticed that there is nothing specific to this bundle.
> In fact, doing this you just allowed your whole application to generate URLs from the command line.
> Please have a look to Symfony's [official documentation](https://symfony.com/doc/current/routing.html#generating-urls-in-commands) 
> for more information.
