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
 * Creates the $_51d array, for viewing properties about a device. 51Degrees is
 * also configured from here, all other aspects of the project use configuration
 * information from this file.
 */

/**
 * Below are global values that control aspects of 51Degrees. These can be set
 * from any PHP file before the 51Degrees PHP file is invoked (eg, include,
 * require).
 */

/**
 * Controls if some objects are cached in an array.
 * Objects are cached by default. Set to FALSE to disable.
 */
$_fiftyone_degrees_use_array_cache = TRUE;
/**
 * Controls if property values are set to their typed values or strings.
 * Defaults to TRUE, set to FALSE to disable.
 */
 // $_fiftyone_degrees_return_strings = FALSE;
 /**
  * Controls the file path that data is read from.
  * Defaults to 51Degrees-Lite.dat.
  */
global $_fiftyone_degrees_data_file_path;
$_fiftyone_degrees_data_file_path = dirname(__FILE__) . '/51Degrees.dat';
 /**
  * If TRUE detection functions are not called automatically by including this
  * script, they have to be called explicitly.
  */
global $_fiftyone_degrees_defer_execution;
// $_fiftyone_degrees_defer_execution = TRUE;
/**
 * Controls which property values should be returned from detection.
 * Greater performance can be gained from a restricted list of properties.
 * By default all values are returned.
 */
// $_fiftyone_degrees_needed_properties = array('IsMobile', 'HardwareModel', 'PlatformName', 'BrowserName');

/**
 * Controls the maximum width an image can be resized too. This can be used to
 * control server load if many images are being processed. Set to 0 to have no
 * limit.
 */
global $_fiftyone_degrees_max_image_width;
$_fiftyone_degrees_max_image_width = 0;

/**
 * Controls the maximum height an image can be resized too. This can be used to
 * control server load if many images are being processed. Set to 0 to have no
 * limit.
 */
global $_fiftyone_degrees_max_image_height;
$_fiftyone_degrees_max_image_height = 0;

/**
 * Specifies what the width parameter should be for an optimised image url.
 * Defaults to 'w'.
 */
global $_fiftyone_degrees_image_width_parameter;
$_fiftyone_degrees_image_width_parameter = 'w';

/**
 * Specifies what the height parameter should be for an optimised image url.
 * Defaults to 'h'.
 */
global $_fiftyone_degrees_image_height_parameter;
$_fiftyone_degrees_image_height_parameter = 'h';

/**
 * If an image is requested with a width or height set to 'auto', the
 * parameter will be changed to the value set in 'defaultAuto'. This is most
 * useful for clients without javascript that should still be served images,
 * Defaults to 50.
 */
 global $_fiftyone_degrees_default_auto;
 $_fiftyone_degrees_default_auto = 50;

/**
 * Sets a factor images dimensions must have. Image sizes are rounded down to
 * nearest multiple. This can be used to control server load if many images are
 * being processed.
 */
global $_fiftyone_degrees_image_factor;
$_fiftyone_degrees_image_factor = 1;

require_once '51Degrees_data_reader.php';

if ((isset($_fiftyone_degrees_defer_execution) &&
  $_fiftyone_degrees_defer_execution === TRUE) === FALSE) {
  global $_51d;
  $_51d = fiftyone_degrees_get_device_data($_SERVER['HTTP_USER_AGENT']);
}
