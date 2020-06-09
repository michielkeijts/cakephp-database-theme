<?php
/**
 * Configuration file for the Notifications Plugin. Or include this in app.php
 */
return [
	'CakeDatabaseThemes' => [
        'pluginDir' => [
            TMP . 'plugins' . DS
        ],
        // does not create files on save, but tries to built files when requested this helps when the cache (TMP) dir is used and flushed many times
        'lazyLoad' => TRUE  
	]
];
