<?php

date_default_timezone_set('America/Toronto');

require('../view.php');

// Set the path to the views
View::setViewPath(__DIR__ . '/views/');

// Load the page template
$view = new View('page');
$view->title('Home - Example Site');

$view->render();
