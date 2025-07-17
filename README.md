## Использовал

- PHP PHP 8.3.21
- Yii2 2.0.53
- NodeJS v22.15.0
- Ratchet (cboden/ratchet) v0.4.4
- SQLite

## Установка

1. Установите зависимости:
```bash
composer install
```

2. Если тестировать через script.js
```bash
npm install ws readline
```

3. Возможно создать файл SQLite, если при миграции не создается
```bash
touch runtime/db.sqlite
```

# Использование

1. Миграции
```bash
php ./yii migrate
```

2. Создание юзера и получение токена
```bash
php ./yii user/create
```

3. Запуск вебсокета
```
php ./yii websocket/start
```

4. Вставить токен в script.js в const TOKEN

5. Запустить
```bash
node script.js
```

### Порт вебсокета меняется в app\commands\WebSocketController::actionStart()

### URL и PORT script.js меняется в начале файла в const 