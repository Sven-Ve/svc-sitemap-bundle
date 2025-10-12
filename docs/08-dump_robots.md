# Generating robots.txt

There are two ways to generate your robots.txt file: via console command (recommended) or dynamically via a controller.

## Console command (recommended)

This is the recommended method for creating the robots.txt file statically. Depending on how frequently your application changes, we recommend updating this file via crontab or during application deployment.

### Command Usage

```shell
bin/console svc:robots.txt:create --help
Description:
  Create the robots.txt file

Usage:
  svc:robots.txt:create [options]

Options:
  -P, --path=PATH       Directory of the robots.txt file
  -F, --file=FILE       Filename of the robots.txt file
```

### Basic Example

```shell
# Create robots.txt in the default location (public/robots.txt)
bin/console svc:robots.txt:create

# Output:
# Create robots.txt
# ==================
# [OK] 3 user agents written in /path/to/project/public/robots.txt
```

### Custom Path and Filename

```shell
# Create robots.txt in a custom location
bin/console svc:robots.txt:create --path=/var/www/html --file=custom-robots.txt

# Create with short options
bin/console svc:robots.txt:create -P /var/www/html -F custom-robots.txt
```

### Automated Generation

#### Via Crontab

Update robots.txt daily at midnight:

```cron
0 0 * * * cd /path/to/project && php bin/console svc:robots.txt:create
```

#### Via Deployment Script

Add to your deployment script (e.g., deploy.sh):

```bash
#!/bin/bash
# ... other deployment steps ...

# Generate robots.txt
php bin/console svc:robots.txt:create

# ... other deployment steps ...
```

### Command Features

- **Locking:** The command uses locking to prevent concurrent execution
- **Error Handling:** If generation fails, the command returns a failure status with error details
- **Flexible Paths:** Override default paths via command options or bundle configuration

## Controller (dynamic generation)

If your robots.txt rules change frequently or depend on runtime conditions, you can generate robots.txt dynamically via a controller. This outputs the content directly without creating a static file.

### Controller Example

```php
<?php

namespace App\Controller;

use Svc\SitemapBundle\Robots\RobotsCreator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RobotsController extends AbstractController
{
    #[Route('/robots.txt', name: 'app_robots')]
    public function robots(RobotsCreator $robotsCreator): Response
    {
        list($text, $userAgentCount) = $robotsCreator->create();

        return new Response($text, 200, ['Content-Type' => 'text/plain']);
    }
}
```

### When to Use Dynamic Generation

**Use dynamic generation when:**
- Robots.txt rules depend on user authentication state
- Rules change based on feature flags or A/B tests
- You have a very small site with minimal traffic
- Rules are determined by real-time database queries

**Use static generation when:**
- Rules are mostly static (recommended for most cases)
- You have high traffic (avoids processing overhead)
- You want better performance
- Rules only change during deployment

## Sitemap Reference in robots.txt

To include a reference to your sitemap.xml in robots.txt, configure the `sitemap_url` option:

```yaml
# config/packages/svc_sitemap.yaml
svc_sitemap:
  robots:
    sitemap_url: 'https://example.com/sitemap.xml'
```

This will add a `Sitemap:` line at the end of your robots.txt:

```
User-agent: google
Allow: /

User-agent: *
Disallow: /admin

Sitemap: https://example.com/sitemap.xml
```

## Example Output

Here's a complete example of a generated robots.txt:

```
User-agent: google
Allow: /
Allow: /de/
Allow: /en/
Allow: /public

User-agent: bing
Allow: /
Allow: /public

User-agent: duckduckbot
Allow: /public

User-agent: *
Disallow: /admin
Disallow: /private
Disallow: /api/internal

Sitemap: https://example.com/sitemap.xml
```

## Excluding robots.txt from Git

Depending on your deployment strategy, you may want to exclude robots.txt from version control to avoid deploying test/staging rules to production:

```gitignore
# .gitignore
/public/robots.txt
```

## Troubleshooting

### Permission Denied Error

If you get a permission denied error:

```
[ERROR] Cannot write robots.txt to /path/to/public/robots.txt: Permission denied
```

**Solution:** Ensure the web server has write permissions:

```bash
# For Linux/Mac
chmod 775 public/
chown www-data:www-data public/robots.txt  # or your web server user
```

### File Already Exists

The command will overwrite existing robots.txt files. If you want to prevent this, modify the file permissions:

```bash
# Make robots.txt read-only
chmod 444 public/robots.txt
```

### Command Already Running

If you see:

```
[CAUTION] The command is already running in another process.
```

**Solution:** Wait for the other process to finish, or remove the lock file:

```bash
# Find and remove the lock file
rm /tmp/sf.*.svc:robots.txt:create.lock
```

## See Also

- [Static robots.txt configuration](06-robots_static.md) - Configure robots.txt via route options
- [Dynamic robots.txt rules](07-robots_dynamic.md) - Add robots.txt rules via events
- [Configuration](02-config.md) - Bundle configuration options
