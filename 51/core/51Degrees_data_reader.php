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
 * Provides detection functionality by interfacing with the file reader methods.
 */

require_once '51Degrees_file_reader.php';
require_once 'LinkedList.php';

global $_fiftyone_degrees_data_file_path;
if (isset($_fiftyone_degrees_data_file_path) == FALSE) {
  $_fiftyone_degrees_data_file_path = dirname(__FILE__) . '/51Degrees-Lite.dat';
}

function fiftyone_degrees_set_file_handle() {
  global $_fiftyone_degrees_data_file;
  global $_fiftyone_degrees_data_file_path;
  if (!file_exists($_fiftyone_degrees_data_file_path)) {
    $_fiftyone_degrees_data_file_path = dirname(__FILE__) . '/51Degrees-Lite.dat';
  }
  $_fiftyone_degrees_data_file = fopen($_fiftyone_degrees_data_file_path, 'rb');
  
}

/**
 * Returns array of properties associated with the device.
 *
 * @param string $useragent
 *   The useragent of the device.
 *
 * @return array
 *   Array of properties and values.
 */
function fiftyone_degrees_get_device_data($useragent) {
  global $_fiftyone_degrees_use_array_caching;
  global $_fiftyone_degrees_data_file;
  fiftyone_degrees_set_file_handle();

  $debug_info = array();
  $start_time = microtime(TRUE);

  $info = array();

  if ($_fiftyone_degrees_use_array_caching !== FALSE) {
    global $_fiftyone_degrees_cache;
    $_fiftyone_degrees_cache = array();
  }

  $headers = fiftyone_degrees_get_headers();

  $root_char_nodes = fiftyone_degrees_read_root_node_offsets($headers);
  $root_char_nodes_count = count($root_char_nodes);

  // Unpack creates a 1 based array. array merge converts to 0 based.
  $useragent_bytes = array_merge(unpack('C*', $useragent));

  $useragent_length = count($useragent_bytes);
  $current_position = min($useragent_length, $root_char_nodes_count) - 1;

  $matched_node_indexs = array();
  $debug_info['root_nodes_evaluated'] = 0;
  $debug_info['nodes_evaluated'] = 0;
  $debug_info['string_read'] = 0;
  $debug_info['signatures_read'] = 0;
  $debug_info['signatures_compared'] = 0;
  $debug_info['difference'] = 0;

  while ($current_position > 0) {
    $node = fiftyone_degrees_read_node(
      $root_char_nodes[$current_position],
      $headers);

    $debug_info['root_nodes_evaluated']++;
    $node = fiftyone_degrees_evaluate_node(
      $node,
      NULL,
      $useragent_bytes,
      $useragent_length,
      $debug_info,
      $headers);

    if ($node != NULL && $node['is_complete']) {
      // Add this node's index to the list for the match in the correct order.
      $index = fiftyone_degrees_integer_binary_search(
        $matched_node_indexs,
        $node['offset']);

      array_splice($matched_node_indexs, ~$index, 0, $node['offset']);
      // Check from the next character position to the left of this one.
      $current_position = $node['next_char_position'];
    }
    else {
      // No nodes matched at the character position.
      $current_position--;
    }
  }
  $timings = array();
  $timings['node_match_time'] = microtime(TRUE) - $start_time;
  $signatures_checked = count($matched_node_indexs);
  $info['SignaturesChecked'] = $signatures_checked;
  $method = '';
  $timings['signature_match_time'] = microtime(TRUE);
  $matched_signature = fiftyone_degrees_get_signature(
    $matched_node_indexs,
    $useragent_bytes,
    $method,
    $timings,
    $debug_info,
    $headers);

  if ($matched_signature != -1) {
    $best_signature = fiftyone_degrees_read_signature(
      $matched_signature,
      $headers);

    if (isset($lowest_score) == FALSE)
      $lowest_score = 0;
  }
  else {
    $lowest_score = PHP_INT_MAX;
    $best_signature = fiftyone_degrees_read_signature(0, $headers);
    $method = 'none';
  }

  $info['Method'] = $method;
  $timings['signature_match_time'] = microtime(TRUE) - $timings['signature_match_time'];
  $debug_info['signature_string'] = fiftyone_degrees_get_signature_string(
    $best_signature,
    $headers);

  $info['Confidence'] = $lowest_score;

  $profiles = array();
  $filled_components = array();

  $feature_detection_ids = fiftyone_degrees_get_feature_detection_profile_ids();
  foreach ($feature_detection_ids as $id) {
    $profile = fiftyone_degrees_get_profile_from_id($id, $headers);
    // Make sure only one profile for each component can be added.
    if ($profile != NULL &&
      !in_array($profile['component_id'], $filled_components)) {
      $filled_components[] = $profile['component_id'];
      $profiles[] = $profile;
    }
  }

  $timings['profile_fetch_time'] = microtime(TRUE);
  foreach ($best_signature['profile_indexs'] as $profile_offset) {
    $profile = fiftyone_degrees_read_profile($profile_offset, $headers);
    // Check if this profile's component has already been filled.
    if (!in_array($profile['component_id'], $filled_components)) {
      $filled_components[] = $profile['component_id'];
      $profiles[] = $profile;
    }
  }
  $timings['profile_fetch_time'] = microtime(TRUE) - $timings['profile_fetch_time'];

  $timings['property_fetch_time'] = microtime(TRUE);
  $_51d = fiftyone_degrees_get_property_data($profiles, $headers);
  $bandwidth = fiftyone_degrees_get_bandwidth_data();
  if ($bandwidth != NULL) {
    foreach ($bandwidth as $k => $v) {
      $_51d[$k] = $v;
    }
  }

  foreach ($info as $i_k => $i_v) {
    $_51d[$i_k] = $i_v;
  }
  $timings['property_fetch_time'] = microtime(TRUE) - $timings['property_fetch_time'];
  $end_time = microtime(TRUE);
  $duration = $end_time - $start_time;
  $_51d['Time'] = $end_time - $start_time;
  $_51d['debug_timings'] = $timings;
  $_51d['debug_info'] = $debug_info;

  global $_fiftyone_degrees_data_file_path;
  $_51d['DataFile'] = $_fiftyone_degrees_data_file_path;
  return $_51d;
}

function fiftyone_degrees_get_complete_numeric_node(
  $node,
  $current_position,
  $useragent_bytes,
  &$lowest_score,
  &$debug_info,
  $headers) {

  $complete_node = NULL;
  // Check to see if there's a next node which matches
  // exactly.
  $next_node = fiftyone_degrees_get_next_node_index(
    $node,
    $useragent_bytes,
    $debug_info,
    $headers);
  if ($next_node !== -1) {
    $next_node = fiftyone_degrees_read_node($next_node, $headers);
    $complete_node = fiftyone_degrees_get_complete_numeric_node(
      $next_node,
      $current_position,
      $useragent_bytes,
      $lowest_score,
      $debug_info,
      $headers);
  }
  if ($complete_node == NULL && $node['numeric_children_count'] > 0) {
    // No. So try each of the numeric matches in ascending order of
    // difference.
    $target = fiftyone_degrees_position_as_number($node['position'], $useragent_bytes);

    if ($target !== NULL) {
      $state = NULL;
      do {
        $numeric_child = fiftyone_degrees_numeric_node_enumeration(
          $node,
          $state,
          $target,
          $headers);
        $enum_result_offset = 0;
        if ($numeric_child !== NULL) {
          $enum_result_offset = $numeric_child['related_node_offset'];
        }
        if ($numeric_child !== NULL) {
          $enum_node = fiftyone_degrees_read_node(
            $numeric_child['related_node_offset'],
            $headers);

          $complete_node = fiftyone_degrees_get_complete_numeric_node(
            $enum_node,
            $current_position,
            $useragent_bytes,
            $lowest_score,
            $debug_info,
            $headers);

          if ($complete_node != NULL) {
            $difference = abs($target - $numeric_child['value']);
            if ($lowest_score == NULL)
              $lowest_score = $difference;
            else
              $lowest_score += $difference;
            break;
          }
        }
      } while ($state['has_result']);
    }
  }
  $complete_node_offset = 0;
  if ($complete_node !== NULL) {
    $complete_node_offset = $complete_node['offset'];
  }

  if ($complete_node == NULL && $node['is_complete'])
    $complete_node = $node;
  return $complete_node;
}

/**
 * Gets a suitable range for a target integer.
 *
 * @param array $target
 *   The target to create a range for.
 *
 * @return array
 *   The range, as an associative array with the values 'lower' and 'upper'.
 */
function fiftyone_degrees_get_range($target) {
  $ranges = array(10000, 1000, 100, 10, 0);
  $upper = 32768;
  foreach ($ranges as $lower) {
    if ($target >= $lower && $target < $upper)
      return array('lower' => $lower, 'upper' => $upper);
    $upper = $lower;
  }
  // this should never happen
  die('numerical target out of range.');
}

/**
 * Provides a pseudo enumerator for a nodes numeric children against the target.
 *
 * @param array $node
 *   A node with numeric children to iterate over.
 * @param array &$state
 *   A state array to allow enumeration. Check $state['has_value'] === TRUE to
 *   see if the enumeration still has values and if this function should be
 *   called with the same $state array.
 * @param int $target
 *   The numeric value of the substring.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The current NodeNumericIndex, or NULL if there isn't one. 
 */
function fiftyone_degrees_numeric_node_enumeration($node, &$state, $target, $headers) {
  if ($state == NULL) {
    if ($target >= 0 && $target <= 32768) {

      // Get the range in which the comparision values need to fall.
      $state = fiftyone_degrees_get_range($target);
      $state['numeric_children'] = fiftyone_degrees_get_numeric_node_indexs(
        $node,
        $headers);

      $numeric_children_values = array();
      foreach ($state['numeric_children'] as $child) {
        $numeric_children_values[] = $child['value'];
      }
      // Get the index in the ordered list to start at.
      $start_index = fiftyone_degrees_integer_binary_search(
        $numeric_children_values,
        $target);

      if ($start_index < 0)
          $start_index = ~$start_index - 1;

      $low_index = $start_index;
      $high_index = $start_index + 1;

      // Determine if the low and high indexes are in range.
      $state['low_in_range'] = $low_index >= 0 && $low_index < $node['numeric_children_count'] &&
          $state['numeric_children'][$low_index]['value'] >= $state['lower'] && 
          $state['numeric_children'][$low_index]['value'] < $state['upper'];
      $state['high_in_range'] = $high_index < $node['numeric_children_count'] && $high_index >= 0 &&
          $state['numeric_children'][$high_index]['value'] >= $state['lower'] &&
          $state['numeric_children'][$high_index]['value'] < $state['upper'];

      $state['low_index'] = $low_index;
      $state['high_index'] = $high_index;
    }
    else {
      $state = array('has_result' => FALSE);
      return NULL;
    }
  }
  $low_index = $state['low_index'];
  $high_index = $state['high_index'];
  $result_value = NULL;
  $state['has_result'] = $state['low_in_range'] || $state['high_in_range'];

  if ($state['low_in_range'] && $state['high_in_range']) {
    // Get the differences between the two values.
    $low_difference
      = abs($state['numeric_children'][$low_index]['value'] - $target);
    $high_difference
      = abs($state['numeric_children'][$high_index]['value'] - $target);

    // Favour the lowest value where the differences are equal.
    if ($low_difference <= $high_difference) {
      $result_value = $state['numeric_children'][$low_index];

      // Move to the next low index.
      $low_index--;
      $state['low_in_range'] = $low_index >= 0 &&
        $state['numeric_children'][$low_index]['value'] >= $state['lower'] &&
        $state['numeric_children'][$low_index]['value'] < $state['upper'];
    }
    else {
      $result_value = $state['numeric_children'][$high_index];

      // Move to the next high index.
      $high_index++;
      $state['high_in_range'] = $high_index < count($state['numeric_children']) &&
        $state['numeric_children'][$high_index]['value'] >= $state['lower'] &&
        $state['numeric_children'][$high_index]['value'] < $state['upper'];
    }
  }
  elseif ($state['low_in_range']) {
    $result_value = $state['numeric_children'][$low_index];

    // Move to the next low index.
    $low_index--;
    $state['low_in_range'] = $low_index >= 0 &&
      $state['numeric_children'][$low_index]['value'] >= $state['lower'] &&
      $state['numeric_children'][$low_index]['value'] < $state['upper'];
  }
  elseif ($state['high_in_range']) {
    $result_value = $state['numeric_children'][$high_index];

    // Move to the next high index.
    $high_index++;
    $state['high_in_range'] = $high_index < count($state['numeric_children']) &&
      $state['numeric_children'][$high_index]['value'] >= $state['lower'] &&
      $state['numeric_children'][$high_index]['value'] < $state['upper'];
  }

  $state['low_index'] = $low_index;
  $state['high_index'] = $high_index;
  return $result_value;
}

/**
 * Returns the position given within the useragent as a number.
 *
 * @param int $node_position
 *   The node's position in the useragent to start looking from.
 * @param array $useragent_bytes
 *   An array of bytes representing the useragent in ascii values.
 *
 * @return array
 *   A number if one was found, or NULL.
 */
function fiftyone_degrees_position_as_number($node_position, $useragent_bytes) {
  $i = $node_position;
  while ($i >= 0 &&
    $useragent_bytes[$i] >= 48 &&
    $useragent_bytes[$i] <= 57)
    $i--;
  if ($i < $node_position) {
    $i++;
    return fiftyone_degrees_get_number(
      $useragent_bytes,
      $i,
      $node_position);
  }
  return NULL;
}

/**
 * Evaluates the given set of nodes numerically against the useragent.
 *
 * This function should be called if an exact match attempt has already failed.
 * This function will check for numbers in the useragent and compare them
 * against known nodes, looking for the smallest number difference. This is most
 * effective where a device's version number has changed and it is not currently
 * in the dataset.
 *
 * @param array $useragent_bytes
 *   An array of bytes representing the useragent in ascii values.
 * @param array $node_offsets
 *   A list of node offsets that have been found so far.
 * @param int &$lowest_score
 *   The current lowest score.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   A list of matching node offsets. This will also have the node offsets
 *   supplied in the paramters.
 */
function fiftyone_degrees_evaluate_numeric_nodes(
  $useragent_bytes,
  &$node_offsets,
  &$lowest_score,
  &$debug_info,
  $headers) {

  $current_position = count($useragent_bytes) - 1;
  $existing_node_index = count($node_offsets) - 1;

  $lowest_score = NULL;

  $root_node_offsets = fiftyone_degrees_read_root_node_offsets($headers);
  while ($current_position > 0) {
    // $existing_node = fiftyone_degrees_read_node($node_offsets[$existing_node_index], $headers);
    if ($existing_node_index >= 0) {
      $root_node = fiftyone_degrees_get_nodes_root_node(
        $node_offsets[$existing_node_index],
        $headers);
      $root_node_position = $root_node['position'];
    }

    if ($existing_node_index < 0 || $root_node_position < $current_position) {
      $debug_info['root_nodes_evaluated']++;
      $position_root = fiftyone_degrees_read_node(
        $root_node_offsets[$current_position],
        $headers);

      $node = fiftyone_degrees_get_complete_numeric_node(
        $position_root,
        $current_position,
        $useragent_bytes,
        $lowest_score,
        $debug_info,
        $headers);


      if ($node != NULL
      && fiftyone_degrees_get_any_nodes_overlap($node, $node_offsets, $headers)) {
        // Insert the node and update the existing index so that
        // it's the node to the left of this one.

        $index = fiftyone_degrees_integer_binary_search(
          $node_offsets,
          $node['offset']);
        array_splice($node_offsets, ~$index, 0, $node['offset']);
        $existing_node_index = ~$index - 1;

        // Move to the position of the node found as 
        // we can't use the next node incase there's another
        // not part of the same signatures closer.
        $current_position = $node['position'];
      }
      else
        $current_position--;
    }
    else {
        // The next position to evaluate should be to the left
        // of the existing node already in the list.
        $existing_node = fiftyone_degrees_read_node($node_offsets[$existing_node_index], $headers);
        $current_position = $existing_node['position'];

        // Swap the existing node for the next one in the list.
        $existing_node_index--;
    }
  }
  return $node_offsets;
}

/**
 * Returns an array of profile id integers from feature detection.
 *
 * @return array
 *   The profile ids from feature detection, or an empty array if no ids were
 * found.
 */
function fiftyone_degrees_get_feature_detection_profile_ids() {
  if (isset($_SESSION['51D_ProfileIds']) && strlen($_SESSION['51D_ProfileIds']) > 0) {
    $ids = explode('-', $_SESSION['51D_ProfileIds']);
    return $ids;
  }
  elseif (isset($_COOKIE['51D_ProfileIds'])) {
    $_SESSION['51D_ProfileIds'] = $_COOKIE['51D_ProfileIds'];
    $ids = explode('-', $_COOKIE['51D_ProfileIds']);
    return $ids;
  }
  return array();
}

/**
 * Returns the most suitable signature from a list of node indexes.
 *
 * @param array &$matched_node_indexs
 *   The array of node indexes previoulsy matched.
 * @param array $useragent_bytes
 *   An array of bytes representing the useragent in ascii values.
 * @param string &$method
 *   Will have the method used to match. May return 'exact' or 'closest'.
 *   The supplied value has no effect on how this function is executed, and
 *   this value is always overwritten.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The best fitting signature for the useragent from the given nodes.
 */
function fiftyone_degrees_get_signature(
  &$matched_node_indexs,
  $useragent_bytes,
  &$method,
  &$timings,
  &$debug_info,
  &$headers) {

  $matched_signature = fiftyone_degrees_signature_binary_seach(
    $matched_node_indexs,
    $debug_info,
    $headers);

  if ($matched_signature < 0) {
    $timings['numeric_match_time'] = microtime(TRUE);
    // No. So find any other nodes that match if numeric differences
    // are considered.
    $lowest_score = NULL;
    $matched_numeric_nodes = fiftyone_degrees_evaluate_numeric_nodes(
      $useragent_bytes,
      $matched_node_indexs,
      $lowest_score,
      $debug_info,
      $headers);

    // Can a precise match be found based on the nodes?
    $matched_signature = fiftyone_degrees_signature_binary_seach(
    $matched_numeric_nodes,
    $debug_info,
    $headers);

    $timings['numeric_match_time'] = microtime(TRUE) - $timings['numeric_match_time'];

    if ($matched_signature >= 0) {
      // Yes a precise match was found.
      $method = 'numeric';
      return $matched_signature;
    }
    $timings['get_closest_sigs_time'] = microtime(TRUE);;
    if (count($matched_node_indexs) > 0) {
      $sig_indexs = fiftyone_degrees_get_closest_signature_indexs(
        $matched_node_indexs,
        $timings,
        $debug_info,
        $headers);
      $ranked_sig_indexs = array_splice($sig_indexs, 0, $headers['info']['max_signatures']);
      $timings['get_closest_sigs_time'] = microtime(TRUE) - $timings['get_closest_sigs_time'];
      $timings['get_sig_from_rank_time'] = microtime(TRUE);
      $signatures = array();
      foreach($ranked_sig_indexs as $s) {
        $signatures[] = fiftyone_degrees_get_ranked_signature_related_offset($s, $headers);
      }
      $timings['get_sig_from_rank_time'] = microtime(TRUE) - $timings['get_sig_from_rank_time'];
      
      // Store the score that we've got from the numeric difference
      // calculations.
      $starting_score = $lowest_score;
      $timings['eval_nearest_sigs_time'] = microtime(TRUE);
      $matched_signature = fiftyone_degrees_evaluate_signatures(
        $matched_numeric_nodes,
        $signatures,
        FALSE,
        $useragent_bytes,
        $timings,
        $debug_info,
        $headers);

      $method = 'nearest';
      $timings['eval_nearest_sigs_time'] = microtime(TRUE) - $timings['eval_nearest_sigs_time'];
      if($matched_signature === NULL) {
        $method = 'closest';
        $timings['eval_closest_sigs_time'] = microtime(TRUE);
        $matched_signature = fiftyone_degrees_evaluate_signatures(
          $matched_numeric_nodes,
          $signatures,
          TRUE,
          $useragent_bytes,
          $timings,
          $debug_info,
          $headers);
        // Increase the lowest score by the starting value.
        $lowest_score += $starting_score;
        $debug_info['difference'] = $lowest_score;
        $timings['eval_closest_sigs_time'] = microtime(TRUE) - $timings['eval_closest_sigs_time'];
      }
    }
  }
  else {
    $method = 'exact';
  }
  return $matched_signature;
}

function fiftyone_degrees_get_ranked_signature_related_offset($ranked_sig, $headers) {
  global $_fiftyone_degrees_data_file;
  $_fiftyone_degrees_data_file
    = fiftyone_degrees_get_data_file($headers['ranked_signatures_offset'] + ($ranked_sig * 4));
  $index =fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  return $index;
}

function fiftyone_degrees_evaluate_signatures(
  $matched_nodes,
  $signatures,
  $is_closest,
  $useragent_bytes,
  &$timings,
  &$debug_info,
  $headers) {

  if($is_closest === TRUE)
    $time_name = 'closest_match_evaluate_signatures';
  else
    $time_name = 'nearest_match_evaluate_signatures';
  $timings[$time_name] = microtime(TRUE);

  $matched_signature = NULL;

  $lowest_score = PHP_INT_MAX;
  $last_node_offset = $matched_nodes[count($matched_nodes) - 1];
  $last_node_root = fiftyone_degrees_get_nodes_root_node($last_node_offset, $headers);
  $last_node_character = $last_node_root['position'];
  foreach ($signatures as $signature) {
    $result = fiftyone_degrees_evaluate_signature(
      $matched_nodes,
      $signature,
      $useragent_bytes,
      $last_node_character,
      $lowest_score,
      $is_closest,
      $debug_info,
      $headers);
    if ($result === TRUE) {
      $matched_signature = $signature;
    }
  }
  $timings[$time_name] = microtime(TRUE) - $timings[$time_name];
  return $matched_signature;
}

/**
 * Returns the root nodes from the data file.
 *
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   Array of integers containing file offsets to the root nodes.
 */
function fiftyone_degrees_read_root_node_offsets($headers) {
  $root_char_nodes = array();
  $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file($headers['root_node_offset']);
  for ($i = 0; $i < $headers['root_node_count']; $i++) {
    $root_char_nodes[] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  }
  return $root_char_nodes;
}

/**
 * Evaluates the signature against the target useragent.
 *
 * Compares all the characters up to the max length between the signature and
 * the target user agent.
 *
 * @param int $signature_index
 *   The index of the signature to evalute
 * @param array $target_useragent
 *   The useragent as a byte array of ascii values to compare to.
 * @param int &$lowest_score
 *   The lowest score so far. This value may be overwritten if a new lower
 *   score is found.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return bool
 *   TRUE if the signature scores better than the supplied lowest score.
 */
function fiftyone_degrees_evaluate_signature(
  $matched_nodes,
  $signature_index,
  $useragent_bytes,
  $last_node_character,
  &$lowest_score,
  $is_closest,
  &$debug_info,
  $headers) {
  $signature = fiftyone_degrees_read_signature($signature_index, $headers);

  $debug_info['signatures_compared']++;

  $score = fiftyone_degrees_get_signature_score (
    $signature,
    $matched_nodes,
    $useragent_bytes,
    $lowest_score,
    $last_node_character,
    $is_closest,
    $headers);

  if ($score < $lowest_score) {
    $lowest_score = $score;
    return TRUE;
  }
  else {
    return FALSE;
  }
}

/**
 * Steps through the nodes of the signature comparing those that aren't
 * contained in the matched nodes to determine a score between the signature
 * and the target user agent. If that score becomes greater or equal to the
 * lowest score determined so far then stop.
 */
function fiftyone_degrees_get_signature_score (
  $signature,
  $node_offsets,
  $useragent_bytes,
  $lowest_score,
  $last_node_character,
  $is_closest,
  $headers) {
  $sig_length = fiftyone_degrees_get_signature_length($signature, $headers);
  if ($is_closest === TRUE)
    $running_score = abs($last_node_character + 1 - $sig_length);
  else
    $running_score = 0;

  // We only need to check the nodes that are different. As the nodes
  // are in the same order we can simply look for those that are different.
  $match_node_index = 0;
  $signature_node_index = 0;
  while ($signature_node_index < count($signature['node_indexs'])
  && $running_score < $lowest_score) {
    $match_node_offset = $match_node_index >= count($node_offsets)
      ? PHP_INT_MAX : $node_offsets[$match_node_index];
    $signature_node_offset = $signature['node_indexs'][$signature_node_index];
    if ($match_node_offset > $signature_node_offset) {
      // The matched node is either not available, or is higher than
      // the current signature node. The signature node is not contained
      // in the match so we must score it.
      if ($is_closest) {
        $score = fiftyone_degrees_get_score(
          $signature['node_indexs'][$signature_node_index],
          $useragent_bytes,
          $lowest_score,
          $headers);
      }
      else {
        $score = fiftyone_degrees_get_nearest_score(
          $signature['node_indexs'][$signature_node_index],
          $useragent_bytes,
          $headers);
        
      }
      if ($score < 0) {
        return PHP_INT_MAX;
      }
      $running_score += $score;
      $signature_node_index++;
    }
    else if ($match_node_offset == $signature_node_offset) {
      // They both are the same so move to the next node in each.
      $match_node_index++;
      $signature_node_index++;
    }
    else if ($match_node_offset < $signature_node_offset) {
      // The match node is lower so move to the next one and see if
      // it's higher or equal to the current signature node.
      $match_node_index++;
    }
  }

  return $running_score;
}

/**
 * If the sub string is contained in the target but in a different position
 * return the difference between the two sub string positions.
 */
function fiftyone_degrees_get_nearest_score(
  $node_index,
  $useragent_bytes,
  $headers) {

  $node = fiftyone_degrees_read_node($node_index, $headers);
  $index = fiftyone_degrees_get_node_index_in_string($node, $useragent_bytes, $headers);
  if ($index >= 0)
    return abs($node['position'] + 1 - $index);

  // Return -1 to indicate that a score could not be calculated.
  return -1;
}

/**
 * Returns the start character position of the node within the target
 * user agent, or -1 if the node does not exist.
 */
function fiftyone_degrees_get_node_index_in_string($node, $useragent_bytes, $headers)
{
  $characters = fiftyone_degrees_get_node_characters($node, $headers);
  $char_count = count($characters);
  $final_index = $char_count - 1;
  $ua_count = count($useragent_bytes);
  for ($index = 0; $index < $ua_count - $char_count; $index++) {
    for ($node_index = 0, $target_index = $index; 
      $node_index < $char_count && $target_index < $ua_count; 
      $node_index++, $target_index++) {

      if ($characters[$node_index] != $useragent_bytes[$target_index])
          break;
      else if ($node_index == $final_index)
          return $index;
    }
  }
  return -1;
}


/**
 * Calculates the score of the useragent against the given node.
 *
 * @param int $node_offset
 *   The offset of the node to score the useragent against.
 * @param array $useragent_bytes
 --uabytes-desc--
 * @param int $lowest_score
 *   The current lowest score.
 * @param array $headers
 --headers-desc--
 *
 * @return int
 *   The score.
 */
 function fiftyone_degrees_get_score(
  $node_offset,
  $useragent_bytes,
  $lowest_score,
  $headers) {

  $score = 0;
  $node = fiftyone_degrees_read_node($node_offset, $headers);
  $node_characters = fiftyone_degrees_get_node_characters($node, $headers);
  $node_index = count($node_characters) - 1;

  $target_index
    = $node['position'] + fiftyone_degrees_get_node_length($node, $headers);

  // Adjust the score and indexes if the node is too long.
  $useragent_length = count($useragent_bytes);
  if ($target_index >= $useragent_length) {
    $score = $target_index - $useragent_length;
    $node_index -= $score;
    $target_index = $useragent_length - 1;
  }

  while ($node_index >= 0 && $score < $lowest_score) {
    $difference = abs(
      $useragent_bytes[$target_index] - $node_characters[$node_index]);
    if ($difference != 0) {
      $numeric_difference = fiftyone_degrees_get_numeric_difference(
        $node_characters,
        $useragent_bytes,
        $node_index,
        $target_index);
      if ($numeric_difference != 0)
        $score += $numeric_difference;
      else
        $score += $difference;
    }
    $node_index--;
    $target_index--;
  }
  return $score;
}

/**
 * Checks for a numeric difference between the signature and useragent.
 *
 * @param array $node_characters
 *   An the node's charcters as an ascii byte array.
 * @param array $target_characters
 *   The target user agent array.
 * @param int &$node_index
 *   The starting character to be checked in the node array.
 * @param int &$target_index">
 *   The start character position to the checked in the target array.
 *
 * @return int
 *   The numeric difference between the node and the target, or 0 if no
 *   difference was found.
 */
function fiftyone_degrees_get_numeric_difference(
  $node_characters,
  $target_characters,
  &$node_index,
  &$target_index) {

  // Move right when the characters are numeric to ensure
  // the full number is considered in the difference comparison.
  $new_node_index = $node_index + 1;
  $new_target_index = $target_index + 1;
  while ($new_node_index < count($node_characters)
  && $new_target_index < count($target_characters)
  && fiftyone_degrees_get_is_numeric($target_characters[$new_target_index])
  && fiftyone_degrees_get_is_numeric($node_characters[$new_node_index])) {
    $new_node_index++;
    $new_target_index++;
  }
  $node_index = $new_node_index - 1;
  $target_index = $new_target_index - 1;

  // Find when the characters stop being numbers.
  $characters = 0;
  while ($node_index >= 0
  && fiftyone_degrees_get_is_numeric($target_characters[$target_index])
  && fiftyone_degrees_get_is_numeric($node_characters[$node_index])) {
    $node_index--;
    $target_index--;
    $characters++;
  }

  // If there is more than one character that isn't a number then
  // compare the numeric values.
  if ($characters > 1) {
    return abs(
      fiftyone_degrees_get_number($target_characters, $target_index + 1, $characters) -
      fiftyone_degrees_get_number($node_characters, $node_index + 1, $characters));
  }
  return 0;
}

function fiftyone_degrees_get_nodes_overlap($node, $compare_node, $headers) {
  $low_node = $node['position'] < $compare_node['position'] ? $node : $compare_node;
  $high_node = $low_node['position'] == $node['position'] ? $compare_node : $node;

  $low_root_node = fiftyone_degrees_get_nodes_root_node($low_node['offset'], $headers);
  return $low_node['position'] == $high_node['position']
    || $low_root_node['position'] > $high_node;
}

function fiftyone_degrees_get_any_nodes_overlap($node, $other_node_offsets, $headers) {
  foreach($other_node_offsets as $other_node_offset) {
    $other_node = fiftyone_degrees_read_node($other_node_offset, $headers);
    if (fiftyone_degrees_get_nodes_overlap($node, $other_node, $headers))
      return TRUE;
  }
  return FALSE;
}

/**
 * Gets the chacters this node represents.
 *
 * @param array $node
 *   The node to get characters for.
 *
 * @return array
 *   The node's characters as an ascii byte array.
 */
function fiftyone_degrees_get_node_characters($node, $headers) {
  if ($node['character_offset'] !== -1) {
    return fiftyone_degrees_read_ascii_array($node['character_offset'], $headers);
  }
  return NULL;
}

/**
 * Checks if the strings are numerical and gets the difference between them.
 *
 * @param array $target_useragent
 *   An array of bytes representing the useragent in ascii values.
 * @param array $signature
 *   An array of bytes representing the signature in ascii values.
 * @param int $length
 *   The length to check in the string.
 * @param int &$index
 *   The position to start from.
 * @param int &$difference
 *   The numerical difference between the strings.
 */
function fiftyone_degrees_numeric_difference_check(
  $target_useragent,
  $signature,
  $length,
  &$index,
  &$difference) {
  // Record the start index.
  $start = $index;

  // Check that the proceeding characters are both either
  // non-numeric or numeric in each array.
  if ($index > 0 &&
    fiftyone_degrees_get_is_numeric($target_useragent[$index - 1]) !=
    fiftyone_degrees_get_is_numeric($signature[$index - 1]))
    return;

  // Find when the characters stop being numbers.
  while ($index < $length &&
    fiftyone_degrees_get_is_numeric($target_useragent[$index]) &&
    fiftyone_degrees_get_is_numeric($signature[$index]))
    $index++;

  // If there is more than one character that isn't a number then
  // compare the numeric values.
  $end = $index - 1;
  if ($end > $start) {
    $difference = abs(
      fiftyone_degrees_get_number($target_useragent, $start, $end) -
      fiftyone_degrees_get_number($signature, $start, $end));
  }
}

/**
 * Determines if the value is an ASCII numeric value.
 *
 * @param char $value
 *   Byte value to be checked
 *
 * @returns bool
 *   TRUE if the value is an ASCII numeric character
 */
function fiftyone_degrees_get_is_numeric($value) {
  return ($value >= ord('0') && $value <= ord('9'));
}

/**
 * Returns an integer representation of the characters between start and end.
 *
 * Assumes that all the characters are numeric characters.
 * 
 * @param string $string
 *   Array of characters with numeric characters present between start and end.
 * @param int $start
 *   The first character to use to convert to a number
 * @param int $end
 *   The last character to use to convert to a number
 *
 * @return int
 *   The number the substring equates to
 */
function fiftyone_degrees_get_number($string, $start, $end) {
  $value = 0;
  for ($i = $end, $p = 0; $i >= $start; $i--, $p++) {
    $value += pow(10, $p) * ($string[$i] - ord('0'));
  }
  return $value;
}

/**
 * Gets the properties of from all the given profiles.
 *
 * @param array $profiles
 *   The profiles to get properties for.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   Array of property values for the profiles.
 */
function fiftyone_degrees_get_property_data($profiles, $headers) {
  global $_fiftyone_degrees_needed_properties;

  $_51d = array();

  $properties = array();
  for ($i = 0; $i < $headers['property_count']; $i++) {
    $property = fiftyone_degrees_read_property($i, $headers);

    if (fiftyone_degrees_is_needed_property($property))
      $properties[] = $property;
  }

  $device_ids = array();
  $_51d = array();
  foreach ($profiles as $profile) {
    $device_ids[$profile['component_id']] = $profile['unique_id'];

    $profile_values = fiftyone_degrees_get_profile_property_values(
      $profile,
      $properties,
      $headers);
    $_51d = array_merge($_51d, $profile_values);
  }
  ksort($device_ids);
  $_51d['DeviceId'] = implode('-', $device_ids);


  return $_51d;
}

function fiftyone_degrees_get_profile_property_values($profile, $needed_properties, $headers) {
  global $_fiftyone_degrees_return_strings;
  $values = array();
  foreach ($profile['profile_values'] as $value) {
    if (fiftyone_degrees_is_needed_value($needed_properties, $value)) {
      $property_value = fiftyone_degrees_read_property_value($value, $headers);
      $property = fiftyone_degrees_read_property(
        $property_value['property_index'],
        $headers);

      if ($_fiftyone_degrees_return_strings === FALSE)
        $value = $property_value['value'];
      else
        $value = fiftyone_degrees_get_typed_value(
          $property,
          $property_value);
      if ($property['list']) {
        if (!isset($values[$property['name']])) {
          $values[$property['name']] = array();
        }
        $values[$property['name']][] = $value;
      }
      else
        $values[$property['name']] = $value;
    }
  }
  return $values;
}

/**
 * Gets an array of bandwidth data.
 *
 * @return array
 *   An associative array of bandwidth data from this request and session.
 */
function fiftyone_degrees_get_bandwidth_data() {
  $bandwidth = NULL;
  $result = NULL;

  // Check that session and the bandwidth cookie are available.
  if (isset($_SESSION) && isset($_COOKIE['51D_Bandwidth'])) {
    $values = explode('|', $_COOKIE['51D_Bandwidth']);

    if (count($values) == 5) {
      $stats = fiftyone_degrees_get_bandwidth_stats();
      if ($stats != NULL) {
        $last_load_time = $stats['LastLoadTime'];
      }

      $load_start_time = floatval($values[1]);
      $current_time = floatval($values[2]);
      $load_complete_time = floatval($values[3]);
      $page_length = floatval($values[4]);

      $response_time = $load_complete_time - $load_start_time;
      if ($response_time == 0) {
        $page_bandwidth = PHP_INT_MAX;
      }
      else {
        $page_bandwidth = $page_length / $response_time;
      }

      if ($stats != NULL) {
        $stats['LastResponseTime'] = $response_time;
        $stats['last_completion_time'] = $load_complete_time - $last_load_time;
        if (isset($stats['average_completion_time']))
          $stats['average_completion_time']
            = fiftyone_degrees_get_rolling_average(
              $stats['average_completion_time'],
              $response_time,
              $stats['Requests']);
        else
          $stats['average_completion_time'] = $stats['last_completion_time'];

        $stats['AverageResponseTime']
          = fiftyone_degrees_get_rolling_average(
            $stats['AverageResponseTime'],
            $response_time,
            $stats['Requests']);

        $page_bandwidth = fiftyone_degrees_get_rolling_average(
          $stats['AverageBandwidth'],
          $response_time,
          $stats['Requests']);

        $stats['AverageBandwidth'] = $page_bandwidth;
        $stats['LastLoadTime'] = $current_time;
        $stats['Requests']++;
      }
      else {
        $stats = array(
          'LastResponseTime' => $response_time,
          'AverageResponseTime' => $response_time,
          'AverageBandwidth' => $page_bandwidth,
          'LastLoadTime' => $current_time,
          'Requests' => 1,
        );
      }
      $stats['page_length'] = $page_length;
      fiftyone_degrees_set_bandwidth_stats($stats);

      if ($stats['Requests'] >= 3)
        $result = $stats;
    }
  }

  setcookie('51D_Bandwidth', microtime(TRUE));

  return $result;
}

/**
 * Gets the a new average from a previous average with and new value.
 *
 * @param int $current_average
 *   The current average from previous calculations.
 * @param int $value
 *   The new value to add to the average.
 * @param int $count
 *   The number of elements in $current_average.
 */
function fiftyone_degrees_get_rolling_average($current_average, $value, $count) {
  return intval((($current_average * $count) + $value) / ($count + 1));
}

/**
 * Gets previous bandwidth stats.
 *
 * @return array
 *   An array of bandwidth stats, or NULL if none were found.
 */
function fiftyone_degrees_get_bandwidth_stats() {
  if (isset($_SESSION['51D_stats'])) {
    return $_SESSION['51D_stats'];
  }
  return NULL;
}

/**
 * Stores bandwidth stats for future requests.
 *
 * @param array $stats
 *   An array of bandwidth stats to store.
 */
function fiftyone_degrees_set_bandwidth_stats($stats) {
  if (isset($_SESSION)) {
    $_SESSION['51D_stats'] = $stats;
  }
}

/**
 * Returns a typed value of the given value and property.
 *
 * @param array $property
 *   The property.
 * @param array $profile_value
 *   The value of the property.
 *
 * @return mixed
 *   The value of the type string, int, double or bool.
 */
function fiftyone_degrees_get_typed_value($property, $profile_value) {
  $value_string = $profile_value['value'];
  switch ($property['value_type_id']) {
    // String and Javascript.
    case 0:
    case 4:
    default:
      return $value_string;
    // Int.
    case 1:
      return (int) $value_string;
    // Double.
    case 2:
      return (double) $value_string;
    // Bool.
    case 3:
      return $value_string === 'True';
  }
}

/**
 * Indicates if the given property is in the list of required properties.
 *
 * @param array $property
 *   The property to check for.
 *
 * @return bool
 *   TRUE if this property is needed.
 */
function fiftyone_degrees_is_needed_property($property) {
  global $_fiftyone_degrees_needed_properties;
  $is_set = isset($_fiftyone_degrees_needed_properties);
  return $is_set === FALSE || ($is_set === TRUE
    && in_array($property['name'], $_fiftyone_degrees_needed_properties));
}

/**
 * Indicates if the given value index relates to a property in the list.
 *
 * @param array $properties
 *   The array of properties that values are required for.
 * @param int $value_index
 *   The index of the property value.
 *
 * @return bool
 *   TRUE if the value should be used.
 */
function fiftyone_degrees_is_needed_value($properties, $value_index) {
  if ($properties !== NULL) {
    global $_fiftyone_degrees_needed_properties;
    if (isset($_fiftyone_degrees_needed_properties)) {
      foreach ($properties as $prop) {
        if ($value_index >= $prop['first_value_index']
        && $value_index <= $prop['last_value_index'])
          return TRUE;
      }
      return FALSE;
    }
  }
  return TRUE;
}

/**
 * Gets a sorted array of signature indexes from the supplied nodes.
 *
 * @param array $node_indexs
 *   The array of node indexes to get signatures from.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   A sorted array of signature indexes.
 */
function fiftyone_degrees_get_closest_signature_indexs(
  $node_indexs,
  &$timings,
  &$debug_info,
  $headers) {

  
  $node_count = count($node_indexs);
  
  if ($node_count == 1) {
    // There is only 1 list so return that single list.
    $node = fiftyone_degrees_read_node($node_indexs[0], $headers);
    fiftyone_degrees_fill_node_ranked_signatures($node, $headers['info']['max_signatures']);
    $sig_offsets = array();
    foreach ($node['node_ranked_signatures'] as $offset) {
      $sig_offsets[] = $offset;
    }
    return $sig_offsets;
  }
  else {
    $timings['closest_match_node_sort_time'] = microtime(TRUE);  
    $sorted_nodes = array();
    $nodes = array();

    $max_count = 1;
    $iteration = 2;
    for ($i = 0; $i < $node_count; $i++) {
      $node = fiftyone_degrees_read_node($node_indexs[$i], $headers);
      $sorted_nodes[$i] = $node['node_ranked_signature_count'];
      $nodes[] = $node;
    }
    // Sort nodes in ascending order by signature count.
    array_multisort($sorted_nodes, SORT_ASC, $nodes);

    $timings['closest_match_node_sort_time'] = microtime(TRUE) - $timings['closest_match_node_sort_time'];

    $timings['closest_match_node_fill_signatures_time'] = microtime(TRUE);
    for ($i = 0; $i < $node_count; $i++) {
      fiftyone_degrees_fill_node_ranked_signatures($nodes[$i]);
    }
    $timings['closest_match_node_fill_signatures_time'] = microtime(TRUE) - $timings['closest_match_node_fill_signatures_time'];

    $timings['closest_match_filling_linked_list_time'] = microtime(TRUE);

    // Building initial list.
    $linked_list = new LinkedList();
    if (count($nodes) > 0) {
      $node_0_signatures_count = count($nodes[0]['node_ranked_signatures']);
      if ($node_0_signatures_count > $headers['info']['max_signatures']) {
      //  $node_0_signatures_count = $headers['info']['max_signatures'];
      }
      for ($i = 0; $i < $node_0_signatures_count; $i++) {
        $linked_list->addLast(array($nodes[0]['node_ranked_signatures'][$i], 1));
      }
    }

    // Count the number of times each signature index occurs.
    for ($i = 1; $i < $node_count; $i++) {
      $max_count = fiftyone_degrees_get_closest_signatures_for_node(
        $node_count,
        $nodes[$i]['node_ranked_signatures'],
        $linked_list,
        $max_count,
        $iteration,
        $headers);
      $iteration++;
    }
    $timings['closest_match_filling_linked_list_time'] = microtime(TRUE) - $timings['closest_match_filling_linked_list_time'];
    $timings['closest_match_sorting_signature_ranks'] = microtime(TRUE);

    $sig_offsets = array();
    $linked_list->current = $linked_list->first;
    
    while ($linked_list->current !== -1) {
      if ($linked_list->current->value[1] == $max_count) {
        $debug_info['signatures_read']++;
        $sig_offsets[] = $linked_list->current->value[0];
      }
      $linked_list->moveNext();
    }

    $timings['closest_match_sorting_signature_ranks'] = microtime(TRUE) - $timings['closest_match_sorting_signature_ranks'];
    return $sig_offsets;
  }
}

/**
 * Gets signatures that are most featured in the signature index list.
 *
 * This function fills a linked list of signatures depending on highly they're
 * ranked.
 *
 * @param array $signature_index_list
 *   A list of signature indexes to check.
 * @param int $max_count
 *   The amount if times a signature should be seen before being excluded.
 * @param int $iteration
 *   The iteration of this function.
 */
function fiftyone_degrees_get_closest_signatures_for_node(
  $nodes_found,
  $signature_index_list, 
  $linked_list, 
  $max_count,
  $iteration,
  $headers) {

  $signature_index_count = count($signature_index_list);
  if ($signature_index_count > $headers['info']['max_signatures']) {
  //  $signature_index_count > $headers['info']['max_signatures'];
  }
  // If there is point adding any new signature indexes set the
  // threshold reached indicator. New signatures won't be added
  // and ones with counts lower than maxcount will be removed.
  $threshold_reached = $nodes_found - $iteration < $max_count;
  $current = $linked_list->first;
  $signature_index = 0;
  while ($signature_index < $signature_index_count && $current !== -1) {
    if ($current->value[0] > $signature_index_list[$signature_index]) {
      // The base list is higher than the target list. Add the element
      // from the target list and move to the next element in each.
      if ($threshold_reached == FALSE) {
        $current->addBefore(
          array($signature_index_list[$signature_index], 1));
        }
      $signature_index++;
    }
    else if ($current->value[0] < $signature_index_list[$signature_index]) {
      if ($threshold_reached) {
        // Threshold reached so we can removed this item
        // from the list as it's not relevant.
        $next_item = $current->nextNode;
        if ($current->value[1] < $max_count) {
          $current->remove();
        }
        $current = $next_item;
      }
      else {
        $current = $current->nextNode;
      }
    }
    else {
      // They're the same so increase the frequency and move to the next
      // element in each.
      $current->value[1]++;
      if ($current->value[1] > $max_count)
        $max_count = $current->value[1];
      $signature_index++;
      $current = $current->nextNode;
    }
  }
  if ($threshold_reached === FALSE) {
    // Add any signature indexes higher than the base list to the base list.
    while ($signature_index < $signature_index_count) {
      $linked_list->addLast(
        array($signature_index_list[$signature_index], 1));
      $signature_index++;
    }
  }
  return $max_count;
}

/**
 * Gets a profile from the given offset.
 *
 * @param int $offset
 *   The position to look in the data file.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The profile.
 */
function fiftyone_degrees_read_profile($offset, $headers) {
  $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file($offset + $headers['profile_offset']);

  $profile = array();

  $profile['component_id'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
  $profile['unique_id'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $profile['profile_value_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $profile['signature_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $profile['profile_values'] = array();
  for ($i = 0; $i < $profile['profile_value_count']; $i++) {
    $profile['profile_values'][] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  }

  return $profile;
}

/**
 * Returns the property value with the given index.
 *
 * @param int $index
 *   The index of the property value.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The property value.
 */
function fiftyone_degrees_read_property_value($index, $headers) {
  $offset = $headers['values_offset'] + ($index * 14);

  $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file($offset);
  $property_value = array();
  $property_value['index'] = $index;
  $property_value['property_index'] = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $property_value['value_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $property_value['description_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $property_value['url_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $property_value['value'] = fiftyone_degrees_read_ascii($property_value['value_offset'], $headers);
  return $property_value;
}

/**
 * Returns the property with the given index.
 *
 * @param int $index
 *   The index of the property.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The property.
 */
function fiftyone_degrees_read_property($index, $headers) {
  $property = fiftyone_degrees_get_from_cache('property:' . $index);
  if ($property == FALSE) {
    $offset = $headers['property_offset'] + ($index * 44);

    $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file($offset);
    $property = array();
    $property['index'] = $index;
    $property['com_index'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
    $property['display_order'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
    $property['mandatory'] = fiftyone_degrees_read_bool($_fiftyone_degrees_data_file);
    $property['list'] = fiftyone_degrees_read_bool($_fiftyone_degrees_data_file);
    $property['export_values'] = fiftyone_degrees_read_bool($_fiftyone_degrees_data_file);
    $property['is_obsolete'] = fiftyone_degrees_read_bool($_fiftyone_degrees_data_file);
    $property['show'] = fiftyone_degrees_read_bool($_fiftyone_degrees_data_file);
    $property['value_type_id'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
    $property['default_prop_index'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    $property['name_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    $property['description_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    $property['category_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    $property['url_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    $property['first_value_index'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    $property['last_value_index'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    $property['map_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    $property['first_map_index'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

    $property['name'] = fiftyone_degrees_read_ascii($property['name_offset'], $headers);
    fiftyone_degrees_save_to_cache('property:' . $index, $property);
  }
  return $property;
}

/**
 * Gets the index of the signature relating to given node indexes.
 * 
 * @param array $node_indexs
 *   The array of node indexes previously matched.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return int
 *   The index of the signature. Returns the ~ of position the signature
 *   should be inserted into if once cannot be found.
 */
function fiftyone_degrees_signature_binary_seach(
  $node_indexs,
  &$debug_info,
  $headers) {

  $lower = 0;
  $upper = $headers['signatures_count'] - 1;
  $middle = 0;
  while ($lower <= $upper) {
    $debug_info['signatures_read']++;
    $middle = $lower + (int) (($upper - $lower) / 2);
    $signature = fiftyone_degrees_read_signature($middle, $headers);
    $comparison_result = fiftyone_degrees_compare_signature_to_node_indexs(
      $signature,
      $node_indexs);
    if ($comparison_result == 0) {
      return $middle;
    }
    elseif ($comparison_result > 0)
      $upper = $middle - 1;
    else
      $lower = $middle + 1;
  }
  return ~$middle;
}

/**
 * Compares a signature to node indexes.
 *
 * @param array $signature
 *   The signature.
 * @param array $node_indexs
 *   The node indexes.
 *
 * @return int
 *   0 if the signatures and nodes are identical.
 */
function fiftyone_degrees_compare_signature_to_node_indexs($signature, $node_indexs) {
  $signature_node_indexs = count($signature['node_indexs']);
  $nodes_count = count($node_indexs);
  $length = min($signature_node_indexs, $nodes_count);

  for ($i = 0; $i < $length; $i++) {
    $difference = fiftyone_degrees_integer_compare_to($signature['node_indexs'][$i], $node_indexs[$i]);
    if ($difference != 0)
      return $difference;
  }

  if ($signature_node_indexs < $nodes_count)
    return -1;
  if ($signature_node_indexs > $nodes_count)
    return 1;

  return 0;
}

/**
 * A generic implementation of a divide and conquer search for an integer list.
 *
 * @param array $list
 *   List of numbers.
 * @param int $value
 *   Value to search for.
 *
 * @return int
 *   The key of the found value, or a negative value if it is not present.
 */
function fiftyone_degrees_integer_binary_search($list, $value) {
  $lower = 0;
  $upper = count($list) - 1;
  $middle = 0;

  while ($lower <= $upper) {
    // $middle = $lower + (int) (($upper - $lower) / 2);

    $d = ($upper + $lower) / 2;
    $middle = floor($d);
    $comparison_result = fiftyone_degrees_integer_compare_to(
      $list[$middle],
      $value);
    if ($comparison_result == 0)
      return $middle;
    elseif ($comparison_result > 0) {
      $upper = $middle - 1;
    }
    else {
      // Middle must be modified in this instance so the 2's complement can be
      // trusted if the value doesn't exist and this is the last iteration.
      $middle++;
      $lower = $middle;
    }
  }
  return ~$middle;
}

/**
 * Compares two integers.
 *
 * @param int $value
 *   A value
 * @param int $comparison
 *   Another values
 *
 * @return array
 *   0 if the values are the same, 1 if the value is greater than comparison,
 *   and -1 if value is less than comparison.
 */
function fiftyone_degrees_integer_compare_to($value, $comparison) {
  if ($value == $comparison)
    return 0;
  if ($value > $comparison)
    return 1;
  else
    return -1;
}

/**
 * Evalutes child nodes of the current node.
 *
 * This function runs recursively and should only be called from a root node.
 *
 * @param array $current_node
 *   The node with children to be evaluted.
 * @param array $last_node
 *   The parent of the current_node. This should be NULL when calling with a
 *   root node.
 * @param array $target_string
 *   The useragent to evaluate with, as an ascii byte array.
 * @param int $target_length
 *   The position in the target_string to process from.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The most suitable node for the given target_string and length.
 */
function fiftyone_degrees_evaluate_node(
  $current_node,
  $last_node,
  $target_string,
  $target_length,
  &$debug_info,
  $headers) {

  $next_index = fiftyone_degrees_get_next_node_index(
    $current_node,
    $target_string,
    $debug_info,
    $headers);

  if ($next_index > 0) {
    $next_node = fiftyone_degrees_read_node($next_index, $headers);
    if ($next_node['is_complete']) {
      $last_node = $next_node;
    }

    $next_node = fiftyone_degrees_evaluate_node(
      $next_node,
      $last_node,
      $target_string,
      $target_length,
      $debug_info,
      $headers);

    if ($next_node == NULL) {
      return $last_node;
    }
    return $next_node;
  }
  return $last_node;
}

/**
 * Returns a signature with the given index.
 *
 * @param int $index
 *   The index of the signature.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The signature.
 */
function fiftyone_degrees_read_signature($index, $headers) {
  $_fiftyone_degrees_cache = fiftyone_degrees_get_from_cache('sig:' . $index);
  if ($_fiftyone_degrees_cache != FALSE) {
    return $_fiftyone_degrees_cache;
  }
  $signature = array();
  $signature['i'] = $index;
  $signature['file_offset'] = $headers['signatures_offset'] +
    (($headers['signatures_length'] / $headers['signatures_count']) * $index);

  $_fiftyone_degrees_data_file
    = fiftyone_degrees_get_data_file($signature['file_offset']);

  $signature['profile_indexs'] = array();
  for ($i = 0; $i < $headers['info']['signature_profiles_count']; $i++) {
    $profile_index
      = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    if ($profile_index >= 0) {
      $signature['profile_indexs'][] = $profile_index;
    }
  }

  $signature['node_indexs'] = array();
  for ($i = 0; $i < $headers['info']['signature_nodes_count']; $i++) {
    $node_index = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    if ($node_index >= 0) {
      $signature['node_indexs'][] = $node_index;
    }
  }
  fiftyone_degrees_save_to_cache('sig:' . $index, $signature);
  return $signature;
}

/**
 * Gets the number of characters in the signature.
 *
 * @param array $signature
 *   The signaturer to the length of.
 *
 * @return int
 *   The signature length
 */
function fiftyone_degrees_get_signature_length($signature, $headers) {
  $last_node_index = $signature['node_indexs'][count($signature['node_indexs'])-1];
  $last_node = fiftyone_degrees_read_node($last_node_index, $headers);
  $last_node_length = fiftyone_degrees_get_node_length($last_node, $headers);
    return $last_node['position'] + $last_node_length + 1;
}

/**
 * Builds a string representing the nodes in the given signature.
 *
 * @param array $signature
 *   The signature to find the string of.
 *
 * @return string
 *   The signature string.
 */
function fiftyone_degrees_get_signature_string($signature, $headers) {
  $bytes = array();
  $length = fiftyone_degrees_get_signature_length($signature, $headers);
  for($i = 0; $i < $length; $i++) {
    $bytes[$i] = ord('_');
  }
  foreach ($signature['node_indexs'] as $node_index) {
    $node = fiftyone_degrees_read_node($node_index, $headers);
    $node_characters = fiftyone_degrees_get_node_characters($node, $headers);
    $node_char_count = count($node_characters);
    for ($i = 0; $i < $node_char_count; $i++) {
      $bytes[$node['position'] + $i + 1] = $node_characters[$i];
    }
  }
  $string = '';
  foreach($bytes as $byte) {
    $string .= chr($byte);
  }
  return $string;
}

/**
 * Returns a value from cache.
 *
 * This function can be modified for
 * massive speed increase if PHP plugins allowing persistent caching
 * are installed.
 *
 * @param string $key
 *   The key of the cached item.
 *
 * @return mixed
 *   The value from cache, or FALSE of the key was not present.
 */
function fiftyone_degrees_get_from_cache($key) {
  global $_fiftyone_degrees_use_array_cache;
  if ($_fiftyone_degrees_use_array_cache) {
    global $_fiftyone_degrees_cache;
    if (isset($_fiftyone_degrees_cache[$key])) {
      return $_fiftyone_degrees_cache[$key];
    }
  }
  return FALSE;
}

/**
 * Saves a value to cache with the given key.
 *
 * If the key already exists the old value is overwritten.
 *
 * @param string $key
 *   The key to save the value with.
 * @param mixed $value
 *   The value to save.
 */
function fiftyone_degrees_save_to_cache($key, $value) {
  global $_fiftyone_degrees_use_array_cache;
  if ($_fiftyone_degrees_use_array_cache) {
    global $_fiftyone_degrees_cache;
    $_fiftyone_degrees_cache[$key] = $value;
  }
}

/**
 * Returns a string as a byte array from the given position in the data file.
 *
 * @param int $offset
 *   The position in the data file to read the string from.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   An ascii string as a byte array.
 */
function fiftyone_degrees_read_ascii_array($offset, $headers) {
  $_fiftyone_degrees_cache = fiftyone_degrees_get_from_cache('ascii:' . $offset);
  if ($_fiftyone_degrees_cache != FALSE) {
    return $_fiftyone_degrees_cache;
  }
  $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file($headers['ascii_strings_offset'] + $offset);
  $length = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  if ($length == 0) {
    $ascii_array = '';
  }
  else {
    $raw_string = fread($_fiftyone_degrees_data_file, $length - 1);
    $ascii_array = array_merge(unpack('C*', $raw_string));
  }
  fiftyone_degrees_save_to_cache('ascii:' . $offset, $ascii_array);
  return $ascii_array;
}

/**
 * Returns an ascii string from the given position in the data file.
 *
 * @param int $offset
 *   The position in the data file to read the string from.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   An ascii string.
 */
function fiftyone_degrees_read_ascii($offset, $headers) {
  $_fiftyone_degrees_cache
    = fiftyone_degrees_get_from_cache('ascii_s:' . $offset);
  if ($_fiftyone_degrees_cache != FALSE) {
    return $_fiftyone_degrees_cache;
  }
  $_fiftyone_degrees_data_file
    = fiftyone_degrees_get_data_file($headers['ascii_strings_offset'] + $offset);
  $length = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  if ($length <= 1) {
    $ascii_string = '';
  }
  else {
    $ascii_string = fread($_fiftyone_degrees_data_file, $length - 1);
  }
  fiftyone_degrees_save_to_cache('ascii_s:' . $offset, $ascii_string);
  return $ascii_string;
}

/**
 * Gets the node index to process next from the given node.
 *
 * @param array $node
 *   The node.
 * @param array $value
 *   The string value to evaluate the node against.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return int
 *   The index of the next node. -1 if no suitable node is found.
 */
 function fiftyone_degrees_get_next_node_index(
   $node,
   $value,
   &$debug_info,
   $headers) {
  $result = -1;
  $upper = $node['node_index_count'] - 1;
  if ($upper >= 0) {
    $lower = 0;
    $middle = $lower + (int)(($upper - $lower) / 2);

    $node_index = fiftyone_degrees_get_node_index($node, $middle, $headers);
    $node_value = fiftyone_degrees_get_node_index_string(
      $node_index,
      $headers);

    $length = count($node_value);
    $start_index = $node['position'] - $length + 1;
    while ($lower <= $upper) {
      $middle = $lower + (int)(($upper - $lower) / 2);

      $node_index = fiftyone_degrees_get_node_index($node, $middle, $headers);
      $node_index_value = fiftyone_degrees_get_node_index_string(
        $node_index,
        $headers);

      // Increase the number of strings checked.
      if ($node_index['is_string'])
        $debug_info['string_read']++;

      $debug_info['nodes_evaluated']++;

      $root_node = fiftyone_degrees_get_nodes_root_node($node['offset'], $headers);
      $node_index_value_length =
        $root_node['position'] - $node['position'];

      $comparison_result = fiftyone_degrees_value_compare(
      $node_index_value,
      $value,
      $start_index);

      if ($comparison_result == 0) {
        $result = abs($node_index['related_node_index']);
        break;
      }
      else if ($comparison_result > 0)
          $upper = $middle - 1;
      else
          $lower = $middle + 1;
    }
  }
  return $result;
}

/**
 * Gets the string value of the given node index.
 *
 * @param array $node_index
 *   The node index.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The string value of the node index as a byte array.
 */
function fiftyone_degrees_get_node_index_string($node_index, $headers) {
  if ($node_index['is_string']) {
    $characters = fiftyone_degrees_read_ascii_array(
      $node_index['value'],
      $headers);
  }
  else {
    $characters = array();
    $bytes = pack('L', $node_index['value']);
    for ($i = 0; $i < 4; $i++) {
      $o = ord($bytes[$i]);
      if ($o != 0)
        $characters[] = $o;
      else
        break;
    }
  }
  return $characters;
}

/**
 * Evalutes the value of a node index against another value.
 *
 * @param array $node_index
 *   The node index to be evaluated.
 * @param int $start_position
 *   the position in the string to evaluate from.
 * @param array $value
 *   The string to evaluate with.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return int
 *   The difference between the strings. 0 means they were identical.
 */
function fiftyone_degrees_node_index_string_compare($node_index, $start_position, $value, $headers) {
  $characters = fiftyone_degrees_get_node_index_string($node_index, $headers);
  $end = count($characters) - 1;
  for ($i = $end, $v = $start_position + $end; $i >= 0; $i--, $v--) {
    $difference = $characters[$i] - $value[$v];
    if ($difference != 0)
      return $difference;
  }
  return 0;
}

/**
 * Compares a two byte array strings.
 *
 * @param array $characters
 *   Characters to compare
 * @param array $value
 *   A value to compare against
 * @param int $start_index
 *   Start position.
 * @param int $node_value_length
 *   The length to check.
 *
 * @return int
 *   The difference between the values. 0 means the strings are identical.
 */
function fiftyone_degrees_value_compare($characters, $value, $start_index) {
  $char_count = count($characters);
  for ($i = $char_count - 1, $v = $start_index + $char_count - 1; $i >= 0; $i--, $v--) {
    $difference = $characters[$i] - $value[$v];
    if ($difference != 0)
      return $difference;
  }
  return 0;
}

/**
 * Reads a node from the given position in the data file.
 *
 * @param int $offset
 *   The position to get the node in the data file.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The node.
 */
function fiftyone_degrees_read_node($offset, $headers) {
  global $nodes_checked;
  $nodes_checked++;
  $_fiftyone_degrees_cache = fiftyone_degrees_get_from_cache('node:' . $offset);
  if ($_fiftyone_degrees_cache !== FALSE) {
    return $_fiftyone_degrees_cache;
  }

  $_fiftyone_degrees_data_file
    = fiftyone_degrees_get_data_file($headers['node_offset'] + $offset);

  $node = array();
  $node['offset'] = $offset;
  $node['position']
    = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $node['next_char_position']
    = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $node['is_complete'] = $node['next_char_position'] != -32768;
  $node['parent_offset']
    = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $node['character_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $node['node_index_count']
    = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $node['numeric_children_count']
    = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $node['node_ranked_signature_count']
    = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $node['node_indexs_offset'] = ftell($_fiftyone_degrees_data_file);
  $node['node_numeric_children_offset']
    = $node['node_indexs_offset']
    + ($node['node_index_count'] * (1 + 4 + 4));

  $node['node_ranked_signature_offset']
    = $node['node_numeric_children_offset']
    + ($node['numeric_children_count'] * (2 + 4));

  fiftyone_degrees_save_to_cache('node:' . $offset, $node);
  return $node;
}

/**
 * Reads a node index from a given node with the specified index.
 *
 * @param array $node
 *   The node to return an index for.
 * @param int $index
 *   The position of the node index to return.
 *
 * @return array
 *   The node index.
 */
function fiftyone_degrees_get_node_index($node, $index, $headers) {
  $offset = $node['node_indexs_offset'] + ($index * (1 + 4 + 4));
  $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file($offset);

  return array(
    'is_string' =>
      fiftyone_degrees_read_bool($_fiftyone_degrees_data_file),
    'value' =>
      fiftyone_degrees_read_int($_fiftyone_degrees_data_file),
    'related_node_index' =>
      fiftyone_degrees_read_int($_fiftyone_degrees_data_file),
  );
}

/**
 * Reads a numeric node index from a given node with the specified index.
 *
 * @param array $node
 *   The node to return an index for.
 * @param int $index
 *   The position of the numeric node index to return.
 *
 * @return array
 *   The numeric node index.
 */
function fiftyone_degrees_get_numeric_node_index($node, $index, $headers) {
  $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file(
    $node['node_numeric_children_offset']
    + ($index * (2 + 4)));

  return array(
    'value' => fiftyone_degrees_read_short($_fiftyone_degrees_data_file),
    'related_node_index' => fiftyone_degrees_read_int($_fiftyone_degrees_data_file),
  );
}

/**
 * Gets the length of the node.
 *
 * @param array $node
 *   The node to get the length of.
 *
 * @return int
 *   The length of the node.
 */
function fiftyone_degrees_get_node_length($node, $headers) {
  $root = fiftyone_degrees_get_nodes_root_node($node['offset'], $headers);
  return $root['position'] - $node['position'];
}

/**
 * Reads all numeric node indexes for a given node.
 *
 * @param array $node
 *   The node to return an index for.
 *
 * @return array
 *   The numeric node indexes.
 */
function fiftyone_degrees_get_numeric_node_indexs($node, $headers) {
  $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file(
    $node['node_numeric_children_offset']);

  $indexs = array();
  $bytes = fread($_fiftyone_degrees_data_file, 6 * $node['numeric_children_count']);
  $byte_count = strlen($bytes);
  for ($i = 0; $i < $byte_count; $i += 6) {
    $a = ord($bytes[$i]);
    $b = ord($bytes[$i + 1]);
    $c = ord($bytes[$i + 2]);
    $d = ord($bytes[$i + 3]);
    $e = ord($bytes[$i + 4]);
    $f = ord($bytes[$i + 5]);

    $value = $a + ($b << 8);
    $related = $c + ($d << 8) + ($e << 16) + ($f << 24);
    $indexs[] = array(
      'value' => $value,
      'related_node_offset' => $related);
  }

  // for ($i = 0; $i < $node['numeric_children_count']; $i++) {
    // $indexs[] =  array(
      // 'value' => fiftyone_degrees_read_short($_fiftyone_degrees_data_file),
      // 'related_node_offset' => fiftyone_degrees_read_int($_fiftyone_degrees_data_file),
    // );
  // }
  return $indexs;
}

/**
 * Gets the root node of any given node.
 *
 * @param int $node_offset
 *   The node offset to find the root for.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The root node of the supplied node. If a root node is supplied then the
 *   same node is returned.
 */
function fiftyone_degrees_get_nodes_root_node($node_offset, $headers) {
  $node = fiftyone_degrees_read_node($node_offset, $headers);
  if ($node['parent_offset'] >= 0) {
    return fiftyone_degrees_get_nodes_root_node($node['parent_offset'], $headers);
  }
  else {
    return $node;
  }
}

/**
 * Fills the given node with signature information.
 *
 * @param array &$node
 *   The node to fill.
 */
function fiftyone_degrees_fill_node_ranked_signatures(&$node, $limit = 0) {
  $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file($node['node_ranked_signature_offset']);
  if (!isset($node['node_ranked_signatures'])) {
    $node['node_ranked_signatures'] = array();

    if ($limit == 0) {
      $limit = $node['node_ranked_signature_count'];
    }
    for ($node_sig_index = 0; $node_sig_index < $limit; $node_sig_index++) {
      $bytes = fread($_fiftyone_degrees_data_file, 4);
      $value = unpack('l', $bytes);
      $node['node_ranked_signatures'][] = $value[1];
    }
    fiftyone_degrees_save_to_cache('node:'.$node['offset'], $node);
  }
  return $node['node_ranked_signature_count'];
}

/**
 * Gets a pointer to the data file at the given file position.
 *
 * @param int $offset
 *   The position to set the file pointer to. Defaults to 0.
 *
 * @return file
 *   The most suitable node for the given target_string and length.
 */
function fiftyone_degrees_get_data_file($offset = 0) {
  global $_fiftyone_degrees_data_file;
  if ($_fiftyone_degrees_data_file == NULL) {
    die('A 51Degrees data file has not been set.');
  }
  if ($offset >= 0) {
    fseek($_fiftyone_degrees_data_file, $offset);
  }
  return $_fiftyone_degrees_data_file;
}

/**
 * Gets the file offset to a profile with the given index.
 *
 * @param int $index
 *   The index of the profile
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The file offset, or an empty array if no offset was found.
 */
function fiftyone_degrees_get_profile_offset_id_from_index($index, $headers) {
  $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file(
    $headers['profile_offsets_offset'] + ($index * 8));
  $profile_offset = array();
  $profile_offset['profile_id'] = fiftyone_degrees_read_int(
    $_fiftyone_degrees_data_file);
  $profile_offset['offset'] = fiftyone_degrees_read_int(
    $_fiftyone_degrees_data_file) + $headers['profile_offset'];

  return $profile_offset;
}

/**
 * Gets a profile for the given id.
 *
 * @param int $profile_id
 *   The profile id.
 * @param array $headers
 *   Header information from the data file.
 *
 * @return array
 *   The profile, or NULL if no profile was found
 */
function fiftyone_degrees_get_profile_from_id($profile_id, $headers) {
  $lower = 0;
  $upper = $headers['profile_offsets_count'] - 1;
  $middle = 0;

  while ($lower <= $upper) {
    $middle = $lower + (int) (($upper - $lower) / 2);
    $profile_offset = fiftyone_degrees_get_profile_offset_id_from_index($middle, $headers);
    if ($profile_offset['profile_id'] == $profile_id)
      return fiftyone_degrees_read_profile(
        $profile_offset['offset'] - $headers['profile_offset'],
        $headers);
    elseif ($profile_offset['profile_id'] > $profile_id)
      $upper = $middle - 1;
    else
      $lower = $middle + 1;
  }
  return NULL;
}

/**
 * Gets the version of the 51Degrees data file this API supports as a string.
 */
function fiftyone_degrees_get_supported_version() {
  return '3.1';
}

/**
 * Reads the headers of the data file to be used throughout the detection.
 *
 * @return array
 *   The headers of the data file.
 */
function fiftyone_degrees_get_headers() {
  global $_fiftyone_degrees_data_file_path;
  $headers['data_file_path'] = $_fiftyone_degrees_data_file_path;
  $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file(0);
  $headers['info'] = fiftyone_degrees_get_data_info($_fiftyone_degrees_data_file);

  $version = "{$headers['info']['major_version']}.{$headers['info']['minor_version']}";
  $supported_version = fiftyone_degrees_get_supported_version();
  if ($version !== $supported_version) {
    die('An incompatible data file has been supplied. Ensure the lastest 51Degrees data and api are being used.');
  }

  $headers['ascii_strings_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['ascii_strings_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['ascii_strings_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $headers['component_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['component_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['component_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $headers['map_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['map_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['map_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $headers['property_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['property_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['property_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $headers['values_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['values_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['values_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $headers['profile_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['profile_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['profile_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $headers['signatures_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['signatures_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['signatures_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  
  $headers['ranked_signatures_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['ranked_signatures_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['ranked_signatures_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $headers['node_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['node_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['node_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $headers['root_node_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['root_node_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['root_node_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $headers['profile_offsets_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['profile_offsets_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $headers['profile_offsets_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  return $headers;

}

/**
 * Gets data information from the data file.
 *
 * @param file &$_fiftyone_degrees_data_file
 *   The data file.
 *
 * @return array
 *   Returns data information.
 */
function fiftyone_degrees_get_data_info(&$_fiftyone_degrees_data_file) {
  $info = array();
  $info['major_version'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['minor_version'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['build_version'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['revision_version'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);

  $info['version'] = "{$info['major_version']}.{$info['minor_version']}.{$info['build_version']}.{$info['revision_version']}";

  $info['licence_id'] = array();
  for ($i = 0; $i < 16; $i++) {
    $info['licence_id'][] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
  }
  $info['copyright_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['age'] = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $info['min_ua_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['data_set_name_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['format_version_offset'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['published_year'] = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $info['published_month'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
  $info['published_day'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
  $info['next_update_year'] = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $info['next_update_month'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
  $info['next_update_day'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
  $info['device_combinations'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['max_ua_length'] = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $info['min_ua_length'] = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $info['lowest_character'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
  $info['highest_character'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
  $info['max_signatures'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['signature_profiles_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['signature_nodes_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['max_values_count'] = fiftyone_degrees_read_short($_fiftyone_degrees_data_file);
  $info['max_csv_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['max_json_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['max_xml_length'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  $info['max_signatures_closest'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
  return $info;
}

/**
 * Gets the copyright notice.
 *
 * @param array $headers
 *   Header information from the data file.
 *
 * @return string
 *   The copyright notice
 */
function fiftyone_degrees_get_copyright_notice($headers) {
  $notice = fiftyone_degrees_read_ascii($headers['info']['copyright_offset'], $headers);
  return $notice;
}

/**
 * Gets the data set name.
 *
 * @param array $headers
 *   Header information from the data file.
 *
 * @return string
 *   The data set name
 */
function fiftyone_degrees_get_dataset_name($headers) {
  $name = fiftyone_degrees_read_ascii($headers['info']['data_set_name_offset'], $headers);
  return $name;
}
