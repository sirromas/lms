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

require_once '../core/51Degrees_metadata.php';

?>

<html>
	<head>
		<title>51Degrees Property Dictionary</title>
	</head>
	<body>
		<h1>Property Dictionary</h1>
		<p>The list of properties and descriptions explain how to use the available device data. 
		Use the [+] icon to display possible values associated with the property. 
		Use the (?) icons to find out more information about the property or value.
		</p>
		<table>
		<?php
		foreach($_51d_meta_data as $name => $property) {
			echo '<tr>';
			if(isset($property['Url']))
				echo "<td><a href=\"{$property['Url']}\">$name</a></td>";
			else
				echo "<td>$name</td>";
			echo "<td>{$property['Description']}";
			
			if(isset($property['Values'])) {
				echo '<div>';
				foreach($property['Values'] as $value_name => $value) {
					if(isset($value['Url']))
						echo "<span><a href=\"{$value['Url']}\">$value_name, </a></span>";
					else
						echo "<span>$value_name, </span>";
				}
				echo '</div>';
			}
			echo "</td>";
			echo '</tr>';
			
		}
		?>
		</table>
		
	</body>
</html>
		