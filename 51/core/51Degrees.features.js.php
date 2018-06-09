<?php

/**
 * @file
 * Provides image optimiser, bandwidth monitoring and feature detection
 * functionality by generating javascript from 51Degrees device data.
 */

header('Content-type: text/javascript');

header('Vary: User-Agent');
header('Cache-Control: public');

require_once '51Degrees.php';
require_once '51Degrees_metadata.php';

$_fiftyone_degrees_param_count = count($_GET);

function fiftyone_degrees_write_features() {
  global $_51d;

  $pairs = array();

  foreach ($_51d as $property => $value) {
    if (fiftyone_degrees_should_display_property($property)) {
      if (is_array($value)) {
        if (is_string($value[0])) {
          $value = '["' . implode('", "', $value) . '"]';
        }
        else {
          $value = '[' . implode(', ', $value) . ']';
        }
      }
      elseif (is_string($value)) {
        $value = "\"$value\"";
      }
      elseif (is_bool($value)) {
        $value = $value ? 'true' : 'false';
      }
      $clean_property = str_replace ("/", "", $property);
      $pairs[] = "$clean_property: $value";
    }
  }
  echo 'var FODF = {';
  echo implode(', ', $pairs);
  echo '};';
}

function fiftyone_degrees_should_display_property($property_name) {
global $_51d_meta_data;
global $_fiftyone_degrees_param_count;
  if (array_key_exists($property_name, $_51d_meta_data)
    && $_51d_meta_data[$property_name]['ValueType'] != 'javascript') {

    if ($_fiftyone_degrees_param_count > 0) {
      return array_key_exists($property_name, $_GET);
    }
    return TRUE;
  }
  return FALSE;
}

echo fiftyone_degrees_write_features();

