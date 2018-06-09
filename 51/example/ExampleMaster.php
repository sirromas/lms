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

function fiftyone_degrees_echo_header() {
?>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
<link id="LinkStyleDefault" runat="server" rel="Stylesheet" type="text/css" href="Default.css" />
<?php
}

function fiftyone_degrees_echo_properties($properties) {

  foreach ($properties as $property => $value) {
    if ($property == 'debug_info' || $property == 'debug_timings') {
      foreach ($value as $info => $result) {
        echo "<p>$property - $info: $result</p>";
      }
    }
    else {
      if (is_array($value))
        $value = implode('|', $value);
      if (is_bool($value))
        $value = $value === TRUE ? 'True' : 'False';
      echo "<p>$property: $value</p>";
    }
  }
}

function fiftyone_degrees_echo_menu() {
  $pathinfo = pathinfo($_SERVER['PHP_SELF']);
  $dir = $pathinfo['dirname'];
  ?>
  <div class="menu">
    <ul>
      <li class="">
        <a id="Menu_MenuItem_0" href="<?php echo "$dir"; ?>/Tester.php">Tester</a>
      </li>
      <li class="">
        <a id="Menu_MenuItem_1" href="<?php echo "$dir"; ?>/Dictionary.php">Properties</a>
      </li>
      <li class="">
        <a id="Menu_MenuItem_2" href="<?php echo "$dir"; ?>/Devices.php">Explore Devices</a>
      </li>
      <li class="">
        <a id="Menu_MenuItem_3" href="<?php echo "$dir"; ?>/Gallery.php">Image Gallery</a>
      </li>
    </ul>
  </div>
  <?php
}