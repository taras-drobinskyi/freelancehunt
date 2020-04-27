
# Freelancehunt Stats

This is a test task for freelancehunt.com
Original task you can find at https://github.com/freelancehunt/code-test

# Installation

    git clone
    cd freelancehunt
    composer install
    
Add connection configuration for DB in config/config.php

    define('DB_TYPE', 'mysql');
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'freelancehunt');
    define('DB_USER', 'root');
    define('DB_PASS', '111111');
    define('DB_CHARSET', 'utf8mb4');
    
    define('FREELANCE_API_TOKEN', '');
    
Start the server

    cd public
    php -S localhost:8001

