<?php

declare(strict_types=1);

/*
 * This file is part of the SvcSitemap bundle.
 *
 * (c) 2026 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Tests\Standards;

/**
 * Assert all PHP files doc blocks respect conventions.
 */
final class ClassCommentDocBlockTest extends StandardsTestCase
{
    /**
     * Sources classes/interfaces/traits must have doc blocks.
     * - `@author` annotation is forbidden : use `git log --reverse` instead.
     */
    public function testSources(): void
    {
        foreach (self::getSourceFiles() as ['class' => $class]) {
            /** @phpstan-ignore argument.type */
            $doc = (new \ReflectionClass($class))->getDocComment();
            self::assertNotFalse($doc, "Class \"{$class}\" must have comment docblock");
            self::assertStringNotContainsString(
                '@author',
                $doc,
                "Class \"{$class}\" comment docblock does not contains @author annotation"
            );
        }
    }

    /**
     * Tests classes/interfaces/traits can have doc blocks.
     * - `@author` annotation is forbidden : use `git log --reverse` instead.
     */
    public function testTests(): void
    {
        foreach (self::getSourceFiles() as ['class' => $class]) {
            /** @phpstan-ignore argument.type */
            $doc = (new \ReflectionClass($class))->getDocComment();
            if ($doc === false) {
                continue;
            }
            self::assertStringNotContainsString(
                '@author',
                $doc,
                "Class \"{$class}\" comment docblock does not contains @author annotation"
            );
        }
    }
}
