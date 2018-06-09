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
require_once '../core/51Degrees.php';
?>

<html>
<head>
<title>51Degrees Image Optimser Gallery</title>
<?php fiftyone_degrees_echo_header(); ?>
</head>
<body>
<?php
fiftyone_degrees_echo_menu();
if (array_key_exists('ua', $_GET)) {
  $ua = $_GET['ua'];
}
else {
  $ua = $_SERVER['HTTP_USER_AGENT'];
}
?>
<div id="Content">
<form action="Tester.php" method="get">
  Useragent: <input type="text" name="ua" value="<?php echo $ua; ?>" />
  <input type="submit" value="Submit">
</form>
<?php

$properties = fiftyone_degrees_get_device_data($ua);
fiftyone_degrees_echo_properties($properties);

?>
</div>
</body>
</html>