<?php

$list = array();

foreach ($model->areas as $area) {
	$list[] = $area->title;
}

echo implode(', ',$list);
