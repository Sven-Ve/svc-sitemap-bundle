# Generating sitemap.xml

There are two ways to generate your sitemap.xml file: via console command (recommended) or dynamically via a controller.

## Console Command (Recommended)

This is the recommended method for creating the sitemap.xml file statically. Depending on how frequently your application changes, we recommend updating this file via crontab or during application deployment.

### Command Usage

```bash
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

### Basic Example

```bash
# Create sitemap.xml in the default location (public/sitemap.xml)
bin/console svc:sitemap:create_xml

# Output:
# Create sitemap.xml
# ==================
# [OK] 150 urls written in /path/to/project/public/sitemap.xml (12345 bytes)
```

### Custom Path and Filename

```bash
# Create sitemap.xml in a custom location
bin/console svc:sitemap:create_xml --path=/var/www/html --file=my-sitemap.xml

# Create with short options
bin/console svc:sitemap:create_xml -P /var/www/html -F my-sitemap.xml
```

### GZIP Compression

Enable GZIP compression to reduce file size (recommended for large sitemaps):

```bash
# Create compressed sitemap.xml.gz
bin/console svc:sitemap:create_xml --gzip

# This creates both:
# - public/sitemap.xml (uncompressed)
# - public/sitemap.xml.gz (compressed)
```

### Automated Generation

#### Via Crontab

Update sitemap.xml daily at 3 AM:

```cron
0 3 * * * cd /path/to/project && php bin/console svc:sitemap:create_xml --gzip
```

Update every hour:

```cron
0 * * * * cd /path/to/project && php bin/console svc:sitemap:create_xml
```

#### Via Deployment Script

Add to your deployment script (e.g., `deploy.sh`):

```bash
#!/bin/bash
# ... other deployment steps ...

# Generate sitemap with GZIP compression
php bin/console svc:sitemap:create_xml --gzip

# ... other deployment steps ...
```

#### Via CI/CD Pipeline

Example for GitHub Actions:

```yaml
# .github/workflows/deploy.yml
- name: Generate Sitemap
  run: |
    php bin/console svc:sitemap:create_xml --gzip
```

### Command Features

- **Locking:** The command uses locking to prevent concurrent execution
- **Error Handling:** If generation fails, the command returns a failure status with error details
- **Flexible Paths:** Override default paths via command options or bundle configuration
- **Validation:** Automatically validates URL count (max 50,000) and size (max 50MB)

## Controller (Dynamic Generation)

If your sitemap content changes frequently or you have a small site, you can generate the sitemap dynamically via a controller.

### Controller Example

```php
<?php

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
        [$xml, $urlCount] = $sitemapCreator->create();

        return new Response($xml, 200, ['Content-Type' => 'text/xml']);
    }
}
```

### When to Use Dynamic Generation

**Use dynamic generation when:**
- Your sitemap changes in real-time (e.g., user-generated content)
- You have a very small site with minimal traffic
- Sitemap depends on user context or permissions
- You don't have access to cron jobs or deployment scripts

**Use static generation when:**
- You have moderate to high traffic (recommended for most cases)
- Sitemap content is relatively stable
- Better performance is required
- You want to serve GZIP-compressed sitemaps

## Sitemap Limits and Validation

The bundle automatically validates your sitemap against [sitemap.org specifications](https://www.sitemaps.org/protocol.html):

| Limit | Value | Exception |
|-------|-------|-----------|
| Maximum URLs | 50,000 | `SitemapTooLargeException` |
| Maximum file size | 50 MB (uncompressed) | `SitemapTooLargeException` |
| URL format | http/https only | `InvalidArgumentException` |
| Encoding | UTF-8 only | `InvalidArgumentException` |

If your sitemap exceeds these limits, consider:
1. Using [sitemap index files](https://www.sitemaps.org/protocol.html#index) (future feature)
2. Excluding less important pages
3. Filtering old or archived content

## Verifying Your Sitemap

### Local Verification

```bash
# Check if file exists
ls -lh public/sitemap.xml

# View first 20 lines
head -20 public/sitemap.xml

# Count URLs (should be ≤ 50,000)
grep -c "<loc>" public/sitemap.xml

# Check file size (should be ≤ 50 MB)
du -h public/sitemap.xml
```

### Online Validation

1. **Google Search Console:**
   - Submit your sitemap at `https://search.google.com/search-console`
   - Navigate to Sitemaps → Add sitemap → Enter `https://yourdomain.com/sitemap.xml`

2. **XML Sitemap Validators:**
   - [XML Sitemap Validator](https://www.xml-sitemaps.com/validate-xml-sitemap.html)
   - Test in browser: `https://yourdomain.com/sitemap.xml`

3. **robots.txt Reference:**
   ```
   Sitemap: https://yourdomain.com/sitemap.xml
   ```

## Troubleshooting

### Permission Denied Error

If you get a permission denied error:

```
[ERROR] Cannot write sitemap.xml to /path/to/public/sitemap.xml: Permission denied
```

**Solution:** Ensure the web server has write permissions:

```bash
# For Linux/Mac
chmod 775 public/
chown www-data:www-data public/  # or your web server user
```

### File Already Exists

The command will overwrite existing sitemap.xml files by default. If you want to prevent this:

```bash
# Make sitemap.xml read-only
chmod 444 public/sitemap.xml
```

### Command Already Running

If you see:

```
[CAUTION] The command is already running in another process.
```

**Solution:** Wait for the other process to finish, or remove the lock file:

```bash
# Find and remove the lock file
rm /tmp/sf.*.svc:sitemap:create_xml.lock
```

### Empty Sitemap

If your sitemap.xml is empty or contains no URLs:

1. **Check route configuration:** Ensure routes have `#[Sitemap]` attribute or sitemap options
2. **Verify event subscribers:** If using dynamic routes, ensure event subscriber is registered
3. **Enable debug mode:** Check logs for any exceptions during generation
4. **Test manually:**
   ```bash
   bin/console debug:container SitemapCreator
   bin/console debug:event-dispatcher AddDynamicRoutesEvent
   ```

### URLs Missing

If some URLs are missing from the sitemap:

1. **Check route registration:** `bin/console debug:router | grep route_name`
2. **Verify route options:** Ensure sitemap configuration is correct
3. **Check event subscriber:** Make sure all URLs are added to the container
4. **Validate route names:** Ensure route names match in dynamic event subscribers

## See Also

- [Static routes](03-static_routes.md) - Configure static routes
- [Dynamic routes](04-dynamic_routes.md) - Add dynamic content
- [Configuration](02-config.md) - Configure paths and defaults