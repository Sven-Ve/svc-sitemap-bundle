<?php

/*
 * This file is part of the SvcSitemapBundle package.
 *
 * (c) Sven Vetter <https://github.com/Sven-Ve/svc-sitemap-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Tests\Standards;

/**
 * Assert that all PHP files contains same LICENCE comment docblock.
 */
final class LicenceDocBlockTest extends StandardsTestCase
{
  private const EXPECTED = <<<EOL
/*
 * This file is part of the SvcSitemapBundle package.
 *
 * (c) Sven Vetter <https://github.com/Sven-Ve/svc-sitemap-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
EOL;

  public function testSources(): void
  {
    self::assertFilesDocBlocks(self::getSourceFiles());
  }

  public function testTests(): void
  {
    self::assertFilesDocBlocks(self::getTestFiles());
  }

  /**
   * @param iterable<mixed> $files
   */
  private static function assertFilesDocBlocks(iterable $files): void
  {
    foreach ($files as ['relative' => $relative, 'absolute' => $absolute]) {
      /** @phpstan-ignore argument.type */
      $lines = \array_slice(\file($absolute), 2, 8);
      $lines = \trim(\implode('', $lines));
      self::assertSame(self::EXPECTED, $lines, "File {$relative} contains expected LICENCE docblock");
    }
  }
}
