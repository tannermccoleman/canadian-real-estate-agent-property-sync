<?php

add_image_size('admin-thumb', 80, 9999);

/*************************************************
 * LIBRARIES
 *************************************************/
require_once $AVIATORS_DIR . '/libraries/Twig/Autoloader.php';
require_once $AVIATORS_DIR . '/libraries/Twig/ExtensionInterface.php';
require_once $AVIATORS_DIR . '/libraries/Twig/Extension.php';

require_once $AVIATORS_DIR . '/libraries/wpalchemy/MetaBox.php';
require_once $AVIATORS_DIR . '/libraries/wpalchemy/MediaAccess.php';

require_once $AVIATORS_DIR . '/libraries/creasync_aq_resizer.php';

/*************************************************
 * PLUGINS
 *************************************************/


require_once $AVIATORS_DIR . '/core/helpers.php';