Open Github terminal from your web server root directory.

run "git clone https://github.com/judhisahoo/ledger_app_laravel.git"

run  "cd ledger_app_laravel" 

copy ".env.example" to new ".env" file

add Mysql DB Name,Username,Password if any in the ".env" file in all "DB_" 

run comamnd bellow
-------------------------
composer install
php artisan migrate
php artisan passport:install
php artisan key:generate
php artisan config:clear
php artisan cache:clear
