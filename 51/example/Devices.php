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

require_once 'ExampleMaster.php';
?>

<html>
<head>
<title>51Degrees Image Optimser Gallery</title>
<?php fiftyone_degrees_echo_header(); ?>
</head>
<body>
<?php fiftyone_degrees_echo_menu(); ?>
<div id="Content">

<?php

$_fiftyone_degrees_defer_execution = TRUE;
  require_once '../core/51Degrees.php';
fiftyone_degrees_set_file_handle();
$headers = fiftyone_degrees_get_headers();
$dataset_name = fiftyone_degrees_get_dataset_name($headers);
$use_auto = $dataset_name === 'Ultimate' || $dataset_name === 'Premium';
if ($use_auto) {

  $start = microtime(TRUE);

  $_fiftyone_degrees_use_array_cache = FALSE;
  $_fiftyone_degrees_needed_properties = array('HardwareVendor', 'HardwareModel', 'HardwareImages', 'HardwareName');

  if (array_key_exists('vendor', $_GET)) {
    echo '<div class="deviceExplorerDevices" style="clear:both">';

    $hardware_vendor = $_GET['vendor'];
    fiftyone_degrees_echo_profile_images($hardware_vendor, $headers);
  }
  else {
    echo '<div class="deviceExplorerVendors" style="clear:both">';
    echo '<p>Select a hardware vendor to view the device models they produce.</p>';
    for ($i = 0; $i < $headers['property_count']; $i++) {
      $property = fiftyone_degrees_read_property($i, $headers);
      if ($property['name']  == 'HardwareVendor') {
        $hardware_vendor_id = $property['index'];
        break;
      }
    }
    $vendors = array();
    require_once '../core/51Degrees_metadata.php';
    foreach($_51d_meta_data['HardwareVendor']['Values'] as $vendor => $info) {
      $vendors[] = $vendor;
    }
    foreach($vendors as $vendor) {
      echo "<div class=\"item\"><a href=\"Devices.php?vendor=$vendor\">$vendor</a></div>";
    }
  }
}
else {
echo '<p>A full list of devices, their properties and what they look like can 
be seen using 51Degrees Premium or Ultimate data.</p>';
}

?>
<script>
// constants to control animations
var tickTime = 50; // time in milliseconds a 'tick' lasts for
var transitionTime = 500; // time in milliseconds a fade should last for
var imageDelay = 2000; // time in milliseconds a image will show for without fading

 // used to store image data between ticks
var hoveredImageData = null;

 // fired from img onmouseover event. begins transitions.
function ImageHovered(element, urls) {
    hoveredImageData = new Object();
    hoveredImageData.Element = element; // the html img element to change
    hoveredImageData.Urls = urls; // a string array of image urls in priority order 
    hoveredImageData.Cycle = 1; // the image displayed, ie index of Urls
    hoveredImageData.Ticks = 0; // current tick used for animation
    hoveredImageData.Phase = 'FadeOut'; // the next image will immediately begin loading
    hoveredImageData.NextImage = new Image(); // used to preload image
    hoveredImageData.NextImage.src = hoveredImageData.Urls[1];
    hoveredImageData.IntervalId = setInterval(tick, tickTime); // start ticker and store interval id
}

 // fired from img onmouseoff event. resets image to one with highest priority when unhovered
function ImageUnHovered(element, url) {
    element.src = url;
    UpdateFade(100);
    clearInterval(hoveredImageData.IntervalId);
}

function tick() {
    // switch statement controls which part of the element's animation is in progress and updates it.
    // phase is updated when the tick count reaches to relevant constant defined above.
    switch (hoveredImageData.Phase) {


        case 'FadeOut':
            if (hoveredImageData.NextImage.complete == false)
                return; // return to prevent ticks incrementing
                // convert ticks to fade
                UpdateFade(100 - (transitionTime / tickTime) * hoveredImageData.Ticks);
                if (hoveredImageData.Ticks * tickTime == transitionTime)
                    ChangePhase('NewImage');
            break;

        case 'NewImage':
            hoveredImageData.Element.src = hoveredImageData.NextImage.src;
            hoveredImageData.Cycle++;
            // checks if another image is available, going back to beginning if there isn't
            if (hoveredImageData.Cycle == hoveredImageData.Urls.length)
                hoveredImageData.Cycle = 0;
            hoveredImageData.NextImage.src = hoveredImageData.Urls[hoveredImageData.Cycle];
            ChangePhase('FadeIn');
            break;

        case 'FadeIn':
            // convert ticks to fade
            UpdateFade((transitionTime / tickTime) * hoveredImageData.Ticks);
            if (hoveredImageData.Ticks * tickTime == transitionTime)
                ChangePhase('Delay');
            break;


        case 'Delay':
            // nothing to do except wait for time delay to expire
            if (hoveredImageData.Ticks * tickTime == imageDelay)
                ChangePhase('FadeOut');
            break;

    }
    hoveredImageData.Ticks++;
}

function ChangePhase(phase) {
    hoveredImageData.Phase = phase;
    // ticks are reset when a phase is changed
    hoveredImageData.Ticks = 0;
}

// updates img element's opacity. opacity is a percentage - 100 is fully opaque, 0 is completely transparent
function UpdateFade(opacity) {
    // converts opacity to decimal
    hoveredImageData.Element.style.opacity = opacity / 100;
}
</script>
</body>
</html>

<?php

function fiftyone_degrees_echo_profile_images($hardware_vendor, $headers) {

  // Get needed hardware vendor
  $hardware_vendor_id = -1;
  for ($i = 0; $i < $headers['property_count']; $i++) {
    $property = fiftyone_degrees_read_property($i, $headers);
    if ($property['name']  == 'HardwareVendor') {
      $hardware_vendor_id = $property['index'];
      break;
    }
  }

  $hardware_value_id = -1;
  for ($i = 0; $i < $headers['values_count']; $i++) {
    $property_value = fiftyone_degrees_read_property_value($i, $headers);
    if ($property_value['property_index'] == $hardware_vendor_id
    && $property_value['value'] == $hardware_vendor) {
      $hardware_value_id = $property_value['index'];
      break;
    }
  }

  $offset = 0;
  if (array_key_exists('next', $_GET) && is_numeric($_GET['next'])) {
    $offset = (int)$_GET['next'];
  }

  $profile_offsets = array();
  $profiles = fiftyone_degrees_gallery_get_profiles ($offset, 25, $hardware_value_id, $headers);

  foreach($profiles as $profile) {
    $props = fiftyone_degrees_get_property_data(array($profile), $headers);
    $images = array();
    foreach ($props['HardwareImages'] as $image) {
      $parts = explode("\t", $image);
      $caption = $parts[0];
      $images[] = $parts[1];
    }
    $first_image = $images[0];
    $profile_id = $props['DeviceId'];
    $model = $props['HardwareModel'];
    $name = $props['HardwareName'][0];
    $mouse_over = implode("','", $images);
    echo '<div class="item">';
    echo '<div class="model">';
    echo "<a href=\"Device.php?ProfileId=$profile_id\">$model</a>";
    echo '</div>';
    echo '<div class="image">';
    echo "<a href=\"Device.php?ProfileId=$profile_id\">";
    echo "<img src=\"$first_image\" style=\"height:128px;width:128px;\" ";
    if (count($images) > 1)
      echo "onmouseover=\"ImageHovered(this, new Array('$mouse_over'))\" onmouseout=\"ImageUnHovered(this, '$first_image')\"";
    echo "/>";
    echo '</a>';
    echo '</div>';
    echo '<div class="name">';
    echo "<a href=\"Device.php?ProfileId=$profile_id\">$name</a>";
    echo '</div>';
    echo "</div>";

  }

  echo "<div style=\"clear: both; \" ><a href=\"Devices.php?vendor=$hardware_vendor&next=$offset\">Next</a></div>";

}

function fiftyone_degrees_gallery_get_profiles (&$offset, $limit, $hardware_value_id, $headers) {

  $end_offset = $headers['profile_length'];
  $profiles = array();
  while ($offset < $end_offset) {
    $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file($offset + $headers['profile_offset']);
    $profile = array();
    $profile['component_id'] = fiftyone_degrees_read_byte($_fiftyone_degrees_data_file);
    $profile['unique_id'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    $profile['profile_value_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    $profile['signature_count'] = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
    if ($profile['component_id'] == 0) {
      $_fiftyone_degrees_data_file = fiftyone_degrees_get_data_file($offset + 1 + 4 + 4 + 4 + (4 * 6) + $headers['profile_offset']);
      $profile_hardware_value_id = fiftyone_degrees_read_int($_fiftyone_degrees_data_file);
      if ($profile_hardware_value_id == $hardware_value_id) {
    
        $profile = fiftyone_degrees_read_profile($offset, $headers);
      
      
        //if (in_array($hardware_value_id,  $profile['profile_values'])) {
          $profiles[] = $profile;//var_dump($profile['profile_values']);
          if (count($profiles) >= $limit)
            break;
        //}
      }
      
    }
    $offset += 1 + 4 + 4 + 4 + ($profile['profile_value_count'] * 4) + (4 * $profile['signature_count']);
  }
  return $profiles;
}
