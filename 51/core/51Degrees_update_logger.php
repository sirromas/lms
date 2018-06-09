<?php
  require_once('51Degrees_metadata.php');

  $data_type = $_51d_meta_data["DatasetName"];
  $export_date = $_51d_meta_data["Date"];
?>

<html>

<head>
<title>51Degrees. Data Updater for PHP</title>
<script type="text/javascript" src="51Degrees_Update.js" ></script>
</head>

<body>
You're currently using <?php echo $data_type; ?> data exported on the <?php echo $export_date; ?>.
Press the button below to attempt an update from 51Degrees.com.
<form action="" method="POST" name="ajax">
  <input onclick="fiftyone_degrees_start_updates();" type="BUTTON" value="Update Data"></input>
  <div id="update_message"></div>
</form>
</body>

</html>