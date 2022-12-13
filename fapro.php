<?php

declare(strict_types=1);

/**
 * Font Awesome Pro Downloader v1.0
 * 
 * @Author: Royyan Farrodain
 * @Date: 2021-12-15
 * 
 * READ README.md
 */

if (php_sapi_name() != 'cli') die("This program cannot be run in server mode.");

const VERSION = '6.2.1';

class FontAwesomeProDownloader
{
  private static $fa_url = 'https://site-assets.fontawesome.com/releases/v' . VERSION . '/';
  private static $fa_dir = '';

  /**
   * Download FA Pro CSS file.
   * @return string|null Return absolute path to downloaded css file.
   */
  public static function downloadCSS()
  {
    self::$fa_dir = __DIR__ . '/fa_v' . VERSION . '/';
    $css = self::$fa_dir . 'css/all.css';

    if (!is_dir(self::$fa_dir))         mkdir(self::$fa_dir);
    if (!is_dir(self::$fa_dir . 'css')) mkdir(self::$fa_dir . 'css');

    echo "Downloading all.css file.\r\n";

    if (file_put_contents($css, file_get_contents(self::$fa_url . 'css/all.css'))) {
      echo "File all.css successfully downloaded.\r\n";
      return $css;
    }

    echo "File all.css failed to download.\r\n";
    return NULL;
  }

  public static function parseCSS(string $filename)
  {
    if (!is_file($filename)) {
      die("CSS file {$filename} is not found.\r\n");
    }

    if (!is_dir(self::$fa_dir . 'webfonts')) mkdir(self::$fa_dir . 'webfonts');

    $hFile = fopen($filename, 'r');

    while (($line = fgets($hFile)) !== FALSE) {
      if (strpos($line, 'url(', 0) !== FALSE) {
        $r = self::parseURL($line);

        foreach ($r as $url) {
          $filename = basename($url);
          $location = self::$fa_dir . "webfonts/{$filename}";
          echo "Downloading file {$url} to {$location}\r\n";
          file_put_contents($location, file_get_contents($url));
        }
      }
    }

    echo "All resources have been downloaded successfully.\r\n";

    fclose($hFile);
  }

  public static function parseURL(string $str): array
  {
    $data = [];
    $parse = FALSE;
    $s = '';

    for ($x = 0; $x < strlen($str); $x++) {
      if ($parse) {
        if (substr($str, $x, 1) == ')') {
          $parse = FALSE;
          $data[] = self::$fa_url . substr($s, 2);
          $s = '';
        } else {
          $s .= substr($str, $x, 1);
        }
      }

      if (substr($str, $x, 4) == 'url(') {
        $parse = TRUE;
        $x += 4;
      }
    }

    return $data;
  }

  public static function start()
  {
    if ($cssFile = self::downloadCSS()) {
      self::parseCSS($cssFile);
    }
  }
}

FontAwesomeProDownloader::start();
