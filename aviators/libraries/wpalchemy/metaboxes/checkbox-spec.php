<?php
global $AVIATORS_DIR;
$custom_checkbox_mb = new Creasync_WPAlchemy_MetaBox(array
(
	'id' => '_custom_checkbox_meta',
	'title' => 'Checkbox Inputs',
	'template' => $AVIATORS_DIR . '/wpalchemy/metaboxes/checkbox-meta.php',
));

/* eof */