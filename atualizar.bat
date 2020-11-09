

xcopy "../public_html/css" "public/css" /D /Y

xcopy "../public_html/js" "public/js" /D /Y

xcopy "../public_html/mix-manifest.json" "public/" /F /Y

php artisan migrate --force
php artisan route:cache
php artisan config:cache
composer dump-autoload -o
