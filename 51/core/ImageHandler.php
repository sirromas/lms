<?php

/* *********************************************************************
 * This Source Code Form is copyright of 51Degrees Mobile Experts Limited. 
 * Copyright 2014 51Degrees Mobile Experts Limited, 5 Charlotte Close,
 * Caversham, Reading, Berkshire, United Kingdom RG4 7BY
 * 
 * This Source Code Form is the subject of the following patent 
 * applications, owned by 51Degrees Mobile Experts Limited of 5 Charlotte
 * Close, Caversham, Reading, Berkshire, United Kingdom RG4 7BY: 
 * European Patent Application No. 13192291.6; and 
 * United States Patent Application Nos. 14/085,223 and 14/085,301.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.
 * 
 * If a copy of the MPL was not distributed with this file, You can obtain
 * one at http://mozilla.org/MPL/2.0/.
 * 
 * This Source Code Form is "Incompatible With Secondary Licenses", as
 * defined by the Mozilla Public License, v. 2.0.
 * ********************************************************************* */

/**
 * @file
 * Optimises images by creating and caching images based on values provided in
 * the width and height query strings.
 */

/**
 * Creates, caches and serves an image based on request information.
 */
function fiftyone_degrees_create_image() {
  $parts = parse_url($_GET['src']);
  $root = dirname(__FILE__) . '/';
  $img_path = $root . $parts['path'];

  if (file_exists($img_path)) {
    $data = file_get_contents($img_path);
    $source = imagecreatefromstring($data);
    $sizes = fiftyone_degrees_get_size($source);
    $cache = fiftyone_degrees_get_cache_path($img_path, $sizes['width'], $sizes['height']);

    $img_date = filemtime($img_path);

    // Check if cache has this resized image that's newer than the source.
    $cache_path = "$root{$cache['path']}/{$cache['filename']}.{$cache['extension']}";
    if (file_exists($cache_path) && img_date <= filemtime($cache_path)) {
      if (isset($parts['port'])) {
        $port = ":{$parts['port']}";
      }
      else {
        $port = '';
      }
      $redirect = "{$cache['path']}/{$cache['filename']}.{$cache['extension']}";
      header("Location: $redirect");
    }
    else {
      $resize = imagecreatetruecolor($sizes['width'], $sizes['height']);
	  
	  imagesavealpha($resize, true);
	  
	  $trans_colour = imagecolorallocatealpha($resize, 0, 0, 0, 127);
	  imagefill($resize, 0, 0, $trans_colour);
      // Resize.

      imagecopyresampled($resize, $source, 0, 0, 0, 0, $sizes['width'], $sizes['height'], $sizes['source_width'], $sizes['source_height']);
      // Save to cache.
      if (file_exists($cache['path']) || mkdir("$root{$cache['path']}", 0777, TRUE)) {
        switch ($cache['extension']) {
          case 'jpg':
          case 'jpeg':
            imagejpeg($resize, $cache_path);
            break;

          case 'png':
            imagepng($resize, $cache_path);
            break;
        }
      }
      switch ($cache['extension']) {
        case 'jpg':
        case 'jpeg':
          header('content-type: image/jpeg');
          imagejpeg($resize);
          break;

        case 'png':
          header('content-type: image/png');
          imagepng($resize);
          break;
      }
    }
  }
  else {
    header('HTTP/1.0 404 Not Found');
  }
}

/**
 * Gets the file path of the requested image in cache.
 *
 * This path is points to where the cached the image is located, or where is
 * should be created if it does not exist. This function does not check if the
 * image actually exists.
 *
 * @param string $img_path
 *   The given path of the image
 * @param int $width
 *   The width of the image
 * @param int $height
 *   The height of the image
 *
 * @return array
 *   The path of the cache image.
 */
function fiftyone_degrees_get_cache_path($img_path, $width, $height) {

  $encoded_path = base64_encode($img_path);
  
  // The . itself should not be used.
  $ext_pos = strrpos($img_path, '.') + 1;

  $extension = substr($img_path, $ext_pos);

  $file_name = $encoded_path;
  $chunks = str_split($encoded_path, 5);
  $filename = $chunks[count($chunks) - 1];
  unset($chunks[count($chunks) - 1]);
  $p = "ImageCache/$width/$height/";

  $path = $p . implode("/", $chunks);

  $cache_path = array(
    'path' => $path,
    'filename' => $filename,
    'extension' => $extension);
  return $cache_path;
}

/**
 * Gets the size the image should be resized to.
 *
 * This function takes into account the requested size, as well as the size of
 * the source image and the requesting screen size.
 *
 * @param resource $img_data
 *   The image resource of the source image
 *
 * @return array
 *   The width and height to resize to
 */
function fiftyone_degrees_get_size($img_data) {
  global $_fiftyone_degrees_needed_properties;
  $_fiftyone_degrees_needed_properties = array(
    'ScreenPixelsWidth',
    'ScreenPixelsHeight');

  require_once '51Degrees.php';

  global $_51d;
  global $_fiftyone_degrees_max_image_width;
  global $_fiftyone_degrees_max_image_height;
  global $_fiftyone_degrees_image_factor;
  global $_fiftyone_degrees_image_width_parameter;
  global $_fiftyone_degrees_image_height_parameter;
  global $_fiftyone_degrees_default_auto;

  if (!isset($_fiftyone_degrees_default_auto)) {
    $_fiftyone_degrees_default_auto = 50;
  }

  // The value of the auto flag.
  $auto = 'auto';
  
  if (isset($_fiftyone_degrees_image_width_parameter)) {
    $width_param = $_fiftyone_degrees_image_width_parameter;
  }
  else {
    $width_param = 'w';
  }
  if (isset($_fiftyone_degrees_image_height_parameter)) {
    $height_param = $_fiftyone_degrees_image_height_parameter;
  }
  else {
    $height_param = 'h';
  }
  
  $source_width = imagesx($img_data);
  $source_height = imagesy($img_data);
  $source_ratio = $source_width / $source_height;

  $width_limit = $source_width;
  if (isset($_51d['ScreenPixelsWidth'])) {
    if ($_51d['ScreenPixelsWidth'] > $source_width)
      $width_limit = $_51d['ScreenPixelsWidth'];
  }
  if (isset($_fiftyone_degrees_max_image_width) && $_fiftyone_degrees_max_image_width > 0) {
    $width_limit = min($width_limit, $_fiftyone_degrees_max_image_width);
  }

  $height_limit = $source_height;
  if (isset($_51d['ScreenPixelsHeight'])) {
    if ($_51d['ScreenPixelsHeight'] > $source_height)
      $height_limit = $_51d['ScreenPixelsHeight'];
  }
  if (isset($_fiftyone_degrees_max_image_height) && $_fiftyone_degrees_max_image_height > 0) {
    $height_limit = min($height_limit, $_fiftyone_degrees_max_image_height);
  }

  if (isset($_GET[$width_param])) {
    $w = intval($_GET[$width_param]);
    if ($w != 0) {
      if ($w > $width_limit) {
        $w = $width_limit;
	  }
    }
	else if ($_GET[$width_param] === $auto) {
		$w = $_fiftyone_degrees_default_auto;
	}
  }

  if (isset($_GET[$height_param])) {
    $h = intval($_GET[$height_param]);
    if ($h != 0) {
      if ($h > $height_limit) {
        $h = $height_limit;
	  }
    }
	else if ($_GET[$height_param] === $auto) {
		$h = $_fiftyone_degrees_default_auto;
	}
  }
  if (!isset($w) && !isset($h)) {
    $w = $width_limit;
    $h = $width_limit / $source_ratio;
  }
  elseif (!isset($h)) {
    $h = $w / $source_ratio;
  }
  elseif (!isset($w)) {
    $w = $h * $source_ratio;
  }

  $width = intval($w);
  $height = intval($h);

  if(isset($_fiftyone_degrees_image_factor) && $_fiftyone_degrees_image_factor > 0) {
    $width = $_fiftyone_degrees_image_factor
      * intval(floor($width / $_fiftyone_degrees_image_factor));

    $height = $_fiftyone_degrees_image_factor
      * intval(floor($height / $_fiftyone_degrees_image_factor));
  }

  if ($width < $_fiftyone_degrees_image_factor) {
    $width = $_fiftyone_degrees_image_factor;
  }
  if ($height < $_fiftyone_degrees_image_factor) {
    $height = $_fiftyone_degrees_image_factor;
  }

  return array(
    'width' => $width,
    'height' => $height,
    'source_width' => $source_width,
    'source_height' => $source_height);
}

fiftyone_degrees_create_image();
