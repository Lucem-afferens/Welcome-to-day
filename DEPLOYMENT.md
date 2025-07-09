# 🚀 Настройка автоматического деплоя на Link-Host (FTP)

## Шаг 1: Подготовка сервера

1. **Убедитесь, что FTP доступен** на вашем хостинге
2. **Создайте папку для проекта** на сервере (обычно уже создана)
3. **Проверьте FTP доступ** с помощью любого FTP клиента

## Шаг 2: Настройка GitHub Secrets

В настройках репозитория GitHub (`Settings` → `Secrets and variables` → `Actions`) добавьте:

| Secret | Описание | Пример |
|--------|----------|--------|
| `FTP_SERVER` | FTP сервер | `ftp.your-host.ru` |
| `FTP_USERNAME` | FTP логин | `your_username` |
| `FTP_PASSWORD` | FTP пароль | `your_password` |

## Шаг 3: Настройка веб-сервера

### Для Nginx:
```nginx
server {
    listen 80;
    server_name welcome-to-day.ru;
    root /www/welcome-to-day.ru;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }
}
```

### Для Apache:
```apache
<VirtualHost *:80>
    ServerName welcome-to-day.ru
    DocumentRoot /www/welcome-to-day.ru
    
    <Directory /www/welcome-to-day.ru>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Шаг 4: Тестирование

1. Внесите изменения в код
2. Запушьте в ветку `main`:
   ```bash
   git add .
   git commit -m "Update site"
   git push origin main
   ```
3. Проверьте статус деплоя в GitHub Actions
4. Откройте сайт и убедитесь, что изменения применились

## 🔧 Полезные команды

```bash
# Локальная разработка
npm run dev

# Сборка для продакшена
npm run build

# Предпросмотр сборки
npm run preview
```

## ⚠️ Безопасность

- **Используйте SFTP** вместо FTP если возможно
- **Ограничьте доступ** к FTP только необходимыми IP
- **Регулярно меняйте пароли**
- **Используйте сложные пароли**

## 🆘 Troubleshooting

### Ошибка FTP подключения
- Проверьте правильность FTP_SERVER, FTP_USERNAME, FTP_PASSWORD
- Убедитесь, что FTP доступен на хостинге
- Проверьте настройки FTP на сервере

### Файлы не обновляются
- Проверьте путь в workflow (`/www/welcome-to-day.ru/`)
- Убедитесь, что веб-сервер настроен правильно
- Проверьте права доступа к папке

### Ошибка сборки
- Проверьте версию Node.js (должна быть 18+)
- Убедитесь, что все зависимости установлены
- Проверьте, что папка `dist/` создается после сборки 