<?php
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

require_once '../core/51Degrees.php';

$headers = fiftyone_degrees_get_headers();
$dataset_name = fiftyone_degrees_get_dataset_name($headers);
$use_auto = $dataset_name === 'Enterprise';
$file = $_GET['image'];
var_dump($dataset_name);
var_dump($use_auto);
$path = 'Gallery/' . $file;

global $_fiftyone_degrees_image_width_parameter;
if (isset($_fiftyone_degrees_image_width_parameter))
  $w_param = $_fiftyone_degrees_image_width_parameter;
else
  $w_param = 'w';
global $_fiftyone_degrees_image_height_parameter;
if (isset($_fiftyone_degrees_image_height_parameter))
  $h_param = $_fiftyone_degrees_image_height_parameter;
else
  $h_param = 'h';

if (file_exists($path)) {
  $img = get_image_panel($use_auto, $file);
  echo $img;
}

function get_image_panel($use_auto, $image_name) {
  global $_51d;
  global $w_param;
  global $h_param;
  if ($use_auto) {
    $output = "<img src=\"E.gif\" data-src=\"../core/ImageHandler.php?src=../example/Gallery/$image_name&$w_param=auto\" class=\"GalleryImage\"/>";
  }
  else {
    if (array_key_exists('ScreenPixelsWidth', $_51d) && is_numeric($_51d['ScreenPixelsWidth']) && $_51d['ScreenPixelsWidth'] != 0)
      $width = $_51d['ScreenPixelsWidth'];
    else
      $width = 800;
    $output = "<img src=\"../core/ImageHandler.php?src=../example/Gallery/$image_name&$w_param=$width\" class=\"GalleryImage\" />";
  }
  return $output;
}

?>
</div>
<?php if ($use_auto) { ?>
<script src="../core/51Degrees.core.js.php"></script>
<script>
  new FODIO(<?php echo "'$w_param', '$h_param'";?>);
</script>
<?php } ?>
</body>
</html>