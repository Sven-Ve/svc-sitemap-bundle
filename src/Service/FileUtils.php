<?php

namespace Svc\SitemapBundle\Service;

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
   * @return string Output filename
   *
   * @throws \Exception if the input or output file can not be opened
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

    // Open output file
    $gzFilename = $inFilename . '.gz';
    $mode = 'wb' . $level;
    $gzFile = gzopen($gzFilename, $mode);
    if ($gzFile === false) {
      fclose($inFile);
      throw new \Exception("Unable to open output file: $gzFilename");
    }

    // Stream copy
    $length = 512 * 1024; // 512 kB
    while (!feof($inFile)) {
      /* @phpstan-ignore argument.type */
      gzwrite($gzFile, fread($inFile, $length));
    }

    // Close files
    fclose($inFile);
    gzclose($gzFile);

    // Return the new filename
    return $gzFilename;
  }
}
