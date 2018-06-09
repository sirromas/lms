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
require_once '../core/51Degrees_metadata.php';

?>
<!DOCTYPE html>
<html>
<head>
<title>51Degrees Property Dictionary</title>
<?php fiftyone_degrees_echo_header(); ?>
</head>
<body>
<?php fiftyone_degrees_echo_menu(); ?>
<div class="content">
<div class="propertyDictionary">
<p>The list of properties and descriptions explainations how to use the available device data.</p>
<?php
echo '<table class="item" cellspacing="0" style="border-collapse:collapse;">';
foreach ($_51d_meta_data as $property => $data) {
  echo '<tr><td>';
  echo '<div class="property">';
  echo "<span>$property</span>";
  echo '</div>';
  if (is_array($data) && array_key_exists('Description', $data)) {
    echo '<div class="description">';
    echo "<span>{$data['Description']}</span>";
    echo '</div>';
  }
  echo '</td></tr>';
}
echo '</table>';
?>
</div>
</div>
</body>
</html>