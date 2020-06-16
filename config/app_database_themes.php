<?php
/**
 * Configuration file for the Notifications Plugin. Or include this in app.php
 */
return [
	'CakeDatabaseThemes' => [
        'pluginDir' => TMP . 'plugins' . DS,
        'ignoreTemplatePaths' => [ // ignores templates matching following paths
            'Api','Events', 'Error','Users','Shark','Hannes','Pages','element/Javascript','element/flash','layout/json','layout/rss','layout/admin.php','layout/ajax.php'
        ],
        // does not create files on save, but tries to built files when requested this helps when the cache (TMP) dir is used and flushed many times
        'lazyLoad' => FALSE,
        'cacheConfig'   => 'default'
	]
];
