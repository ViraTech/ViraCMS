<?php

$list = array();
foreach ($model->layouts as $layout) {
	if ($layout->site) {
		$list[$layout->site->shortTitle][] = $layout->title;
	}
}

$site = null;

foreach ($list as $siteTitle => $layouts) {
	if ($site != $siteTitle) {
		if ($site !== null) {
			echo '<br>';
		}
		$site = $siteTitle;
		echo CHtml::tag('strong', array(), $siteTitle) . ': ';
	}
	echo implode(', ', $layouts);
}
