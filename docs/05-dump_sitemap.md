# Dumping sitemap.xml

## Console command (recommended)

This is the recommended variant for creating the sitemap.xml file statically. Depending on how quickly the application changes, we recommend updating this file via crontab or only when the application is re-released.

```shell
bin/console svc:sitemap:create_xml --help
Description:
  Create the sitemap.xml file

Usage:
  svc:sitemap:create_xml [options]

Options:
  -P, --path=PATH       Directory of the sitemap file
  -F, --file=FILE       Filename of the sitemap file
  -G, --gzip|--no-gzip  GZIP the sitemap file (default: false)
```

## Controller (maybe slow)

If there are only a few routes in the application, the sitemap can also be created dynamically, i.e. the latest URLs are always displayed. In this case, no static file is created but the data is output as XML via the controller.

```php
namespace App\Controller;

use Svc\SitemapBundle\Sitemap\SitemapCreator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SitemapController extends AbstractController
{
  #[Route('/sitemap.xml', name: 'app_sitemap')]
  public function sitemap(SitemapCreator $sitemapCreator): Response
  {
    $xml = $sitemapCreator->create()[0];

    return new Response($xml, 200, ['Content-Type' => 'text/xml']);
  }
}
```