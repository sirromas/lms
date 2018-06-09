<?php
require_once 'ExampleMaster.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>51Degrees Image Optimser Gallery</title>
<?php fiftyone_degrees_echo_header(); ?>
</head>
<body>
<?php fiftyone_degrees_echo_menu(); ?>
<div class="content">
<?php

require_once '../core/51Degrees.php';

$headers = fiftyone_degrees_get_headers();
$dataset_name = fiftyone_degrees_get_dataset_name($headers);
$use_auto = $dataset_name === 'Enterprise';

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

$files = scandir('Gallery');

echo '<table id="body_Images" class="gallery" cellspacing="0" style="border-collapse:collapse;">';
echo '<tbody>';
$row_count = 0;
foreach ($files as $file) {
  
  if (ends_with($file, '.jpg')) {
    if ($row_count == 0)
      echo '<tr>';
    $img = get_image_panel($use_auto, $file);
    echo $img;
    $row_count++;
    if ($row_count == 3) {
      echo '</tr>';
      $row_count = 0;
    }
  }
}

echo '</tbody>';
echo '</table>';

function ends_with($haystack, $needle) {
  $length = strlen($needle);
  if ($length == 0) {
      return TRUE;
  }

  return (substr($haystack, -$length) === $needle);
}

function get_image_panel($use_auto, $image_name) {
  global $w_param;
  global $h_param;
  $output = '<td style="width: 33.3%;">';
  $output .= "<a href=\"GalleryImage.php?image=$image_name\" style=\"max-width: 200px;\" >";
  if ($use_auto) {
    $output .= "<img src=\"../core/E.gif\" data-src=\"../core/ImageHandler.php?src=../example/Gallery/$image_name&$w_param=auto\" />";
  }
  else {
    $output .= "<img src=\"../core/ImageHandler.php?src=../example/Gallery/$image_name&$w_param=500\" />";
  }
  $output .= "</a>";
  $output .= "</td>";
  return $output;
}

?>
</div>
<?php if ($use_auto) { ?>
<script src="../core/51Degrees.core.js.php"></script>
<script>
  new FODIO(<?php echo "'$w_param', '$h_param'"; ?>);
</script>
<?php } ?>
</body>
</html>
