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
 * Creates the $_51d_meta_data array, for viewing  information about properties
 * and their values.
 */

$_fiftyone_degrees_defer_execution = TRUE;
require_once '51Degrees.php';

$_51d_meta_data = fiftyone_degrees_get_meta_data();

unset($_fiftyone_degrees_defer_execution);
/**
 * Returns an array with properties in the data set and their values.
 *
 * @return array
 *   Properties and their values.
 */
function fiftyone_degrees_get_meta_data() {
  fiftyone_degrees_set_file_handle();
  $headers = fiftyone_degrees_get_headers();
  $name = fiftyone_degrees_get_dataset_name($headers);
  $cache_file_name = dirname(__FILE__) .
    "/51Degrees_meta_data_cache_{$headers['info']['published_year']}_{$headers['info']['published_month']}_{$headers['info']['published_day']}_$name.cache";

  $cache_file_name = str_replace("\\", "/", $cache_file_name);

  if (file_exists($cache_file_name)) {
    $cache = file_get_contents($cache_file_name);
    $meta_data = unserialize($cache);
    if ($meta_data !== FALSE)
      return $meta_data;
  }

  $meta_data = array();
  $meta_data['Date'] = "{$headers['info']['published_month']}/{$headers['info']['published_day']}/{$headers['info']['published_year']}";
  $meta_data['DatasetName'] = fiftyone_degrees_get_dataset_name($headers);

  $property_names = array();
  $value_indexes = array();
  for ($i = 0; $i < $headers['property_count']; $i++) {
    $property = fiftyone_degrees_read_property($i, $headers);
    $meta_data[$property['name']] = array();
    $meta_data[$property['name']]['Description'] = fiftyone_degrees_read_ascii($property['description_offset'], $headers);
    $meta_data[$property['name']]['List'] = $property['list'];
    if ($property['url_offset'] != -1) {
      $meta_data[$property['name']]['Url'] = fiftyone_degrees_read_ascii($property['url_offset'], $headers);
    }
    $property_names[$i] = $property['name'];
    $meta_data[$property['name']]['Values'] = array();
    $value_indexes[] = array('first_value_index' => $property['first_value_index'], 'last_value_index' => $property['last_value_index']);
    switch ($property['value_type_id']) {
      case 0: // string
      default:
        $value_type = 'string';
        break;
      case 1: // int
        $value_type = 'integer';
        break;
      case 2: // double
        $value_type = 'double';
        break;
      case 3: // bool
        $value_type = 'boolean';
        break;
      case 4: // javaScript
        $value_type = 'javascript';
        break;
    }
    
    $meta_data[$property['name']]['ValueType'] = $value_type;
  }

  foreach ($value_indexes as $value_index) {
    for ($i = $value_index['first_value_index']; $i <= $value_index['last_value_index']; $i++) {
      $property_value = fiftyone_degrees_read_property_value($i, $headers);
      $name = $property_names[$property_value['property_index']];

      $meta_data[$name]['Values'][$property_value['value']] = array();

      if ($property_value['description_offset'] > 0) {
        $meta_data[$name]['Values'][$property_value['value']]['Description'] = fiftyone_degrees_read_ascii($property_value['description_offset'], $headers);
      }
      if ($property_value['url_offset'] > 0) {
        $meta_data[$name]['Values'][$property_value['value']]['Url'] = fiftyone_degrees_read_ascii($property_value['url_offset'], $headers);
      }
    }
  }

  $handle = fopen($cache_file_name, "w");
  if ($handle !== FALSE) {
    $dir = new DirectoryIterator(dirname(__FILE__));
    foreach ($dir as $fileinfo) {
      if ($fileinfo->isFile()) {
        $file_extension = pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION);
        if ($file_extension === "cache" && strpos($fileinfo->getFilename(), '51Degrees_meta_data_cache_') === 0) {
          $path = $fileinfo->getRealPath();
          @unlink($path);
        }
      }
    }
    $cache = serialize($meta_data);
    fwrite($handle, $cache);
  }
  return $meta_data;
}
