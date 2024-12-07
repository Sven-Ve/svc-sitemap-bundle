# Static routes

You just need to configure an option to your route, so the bundle knows you want to expose it.

The supported sitemap parameters are:

 * `"lastmod"`: a valid datetime as string (default: `"now"`)
 * `"changefreq"`: change frequency of your resource, 
 one of the enums `Svc\SitemapBundle\Enum\ChangeFreq::`, (default: `Svc\SitemapBundle\Enum\ChangeFreq::WEEKLY`)
 * `"priority"`: a number between `0` and `1` (default: `0.5`)

> **Note** you can change defaults in the bundle configuration, see  [here](2-config.md) for more information.

## Attribute

```php
<?php

namespace App\Controller;

use Svc\SitemapBundle\Enum\ChangeFreq;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage', options: ['sitemap' => true])]
    public function home()
    {
        //...
    }

    #[Route('/faq', name: 'faq', options: ['sitemap' => ['priority' => 0.7]])]
    public function faqAction()
    {
        //...
    }

    #[Route('/about', name: 'about', options: ['sitemap' => ['priority' => 0.7, 'changefreq' => ChangeFreq::DAILY]])]
    public function aboutAction()
    {
        //...
    }
}
```
