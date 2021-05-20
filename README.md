## 開発用Dockerコンテナの初期設定
```
$docker-compose run node npm install
$docker-compose up -d
$docker-compose exec web composer install
$docker-compose exec web cp ./.env.dev ./.env
$docker-compose exec web php artisan key:generate
$docker-compose exec web php artisan storage:link
```

## Vueのファイルを更新したときにやるコマンド(アセットコンパイル)
```
$docker-compose exec node npm run dev
```

## composer.lockが更新されたときにやるコマンド
```
$docker-compose exec web composer install
```

## package.jsonが更新されたときにやるコマンド
```
$docker-compose run node npm install
```