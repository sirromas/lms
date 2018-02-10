<?php

$url = 'https://www.newsfactsandanalysis.com/lms/current/index.html';
$page = file_get_contents($url);
echo $page;