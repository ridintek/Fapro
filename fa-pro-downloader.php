<?php declare(strict_types=1);

/**
 * Font Awesome Pro Downloader v1.0
 * 
 * @Author: Royyan Farrodain
 * @Date: 2021-12-15
 * 
 * YOU MAY USE THESE SOFTWARE FOR NON-COMMERCIAL USE.
 * BUT YOU SHOULD PURCHASE IT TO SUPPORT DEVELOPERS IF YOU USE PRO VERSION.
 *
 * THIS SOFTWARE WAS INTENDED FOR TESTING UNIT ONLY.
 * WE ARE NOT RESPONSIBLE FOR ANY ILLEGAL ACT FOR USING THIS SOFTWARE.
 *
 */

if (php_sapi_name() != 'cli') die("This program cannot be run in server mode.");

define('FA_URL', 'https://pro.fontawesome.com/releases/v6.0.0-beta3/');

$ds = DIRECTORY_SEPARATOR;

function parseURL(string $str): array
{
  $data = [];
  $parse = FALSE;
  $s = '';
  
  for ($x = 0; $x < strlen($str); $x++) {
    if ($parse) {
      if (substr($str, $x, 1) == ')') {
        $parse = FALSE;
        $data[] = FA_URL . substr($s, 2);
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

$hFile = fopen(__DIR__ . "{$ds}css{$ds}all.css", 'r');

while (($line = fgets($hFile)) !== FALSE) {
  if ($pos = strpos($line, 'url(', 0)) {
    $r = parseURL($line);
    
    if ($r) {
      foreach ($r as $url) {
        $ret = 0;
        $filename = basename($url);
        $location = __DIR__ . "{$ds}webfonts{$ds}{$filename}";
        echo "Downloading file {$url} to {$location}\r\n";
        file_put_contents($location, file_get_contents($url));
      }
    }
  }
}

fclose($hFile);
