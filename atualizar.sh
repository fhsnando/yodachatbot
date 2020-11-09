cd /home/u874504708/domains/minhavenda.com.br/loja/
git pull
echo "Projeto atualizado"
cp -ruTv public/ ../public_html/
echo "pastas e arquivos publicos atualizado"
php artisan migrate --force
php artisan route:cache
php artisan config:cache
composer install --optimize-autoloader --no-dev
composer dump-autoload -o

echo "Dependencias instaladas"
