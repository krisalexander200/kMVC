<?php

// Here you can create custom config objects so you can pick and choose
// a whole separate settings blocks for production vs dev environments, etc.
// Below is an example.
$config['whateverYouNeed'] = array
(
    'benchmark'         => TRUE,
    'host'              => '192.168.1.11', // Production
    'port'              => 3000,
    'identification'    => 'Weblink 3.5',
    'reconnectTimeout'  => 10,
    'maxRetries'        => 3
);
