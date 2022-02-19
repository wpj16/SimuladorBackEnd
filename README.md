CREATE DATABASE TradeTechnology;

CREATE USER 'tradetechnology' WITH PASSWORD 'TradeTechnology';

ALTER USER tradetechnology WITH SUPERUSER;

------------------------------------------------------------

php artisan schema:create
