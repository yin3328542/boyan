<?php

if (!isset($_SERVER['ENVIRONMENT'])) {
    # production
    //$_SERVER['ENVIRONMENT'] = 'production';

    # develop
    // $_SERVER['ENVIRONMENT'] = 'develop';

    # local
    $_SERVER['ENVIRONMENT'] = 'development';
}