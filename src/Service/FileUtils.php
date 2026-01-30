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

namespace Svc\SitemapBundle\Service;

/**
 * file management utilities.
 */
final class FileUtils
{
    /**
     * Compress a file using gzip.
     *
     * Rewritten from Simon East's version here:
     * https://stackoverflow.com/a/22754032/3499843
     *
     * @param string $inFilename Input filename
     * @param int    $level      Compression level (default: 9)
     *
     * @throws \Exception if the input or output file can not be opened
     *
     * @return string Output filename
     */
    public static function gzcompressfile(string $inFilename, int $level = 9): string
    {
        // Is the file gzipped already?
        $extension = pathinfo($inFilename, PATHINFO_EXTENSION);
        if ($extension == 'gz') {
            return $inFilename;
        }

        // Open input file
        $inFile = fopen($inFilename, 'rb');
        if ($inFile === false) {
            throw new \Exception("Unable to open input file: $inFilename");
        }

        try {
            // Open output file
            $gzFilename = $inFilename . '.gz';
            $mode = 'wb' . $level;
            $gzFile = gzopen($gzFilename, $mode);
            if ($gzFile === false) {
                throw new \Exception("Unable to open output file: $gzFilename");
            }

            try {
                // Stream copy
                $length = 512 * 1024; // 512 kB
                while (!feof($inFile)) {
                    /* @phpstan-ignore argument.type */
                    gzwrite($gzFile, fread($inFile, $length));
                }
            } finally {
                // Always close gzip file
                gzclose($gzFile);
            }
        } finally {
            // Always close input file
            fclose($inFile);
        }

        // Return the new filename
        return $gzFilename;
    }
}
