<?php

return [
    'general' => [
        'journal_mode' => 'WAL',
        'auto_vacuum' => 'incremental',
        'page_size' => 32768, // 32 KB
        'busy_timeout' => 5000, // 5 seconds
        'cache_size' => -20000,
        'foreign_keys' => 'ON',
        'mmap_size' => 134217728, // 128 MB
        'temp_store' => 'MEMORY',
        'synchronous' => 'NORMAL',
    ],

    // You can override the general settings for specific database connections, defined in config/database.php
    'databases' => [
        'example_connection' => [
            'busy_timeout' => 10000, // override general settings and set 10 seconds
            'temp_store' => null, // unset temp_store from general settings
        ],
    ],
];
