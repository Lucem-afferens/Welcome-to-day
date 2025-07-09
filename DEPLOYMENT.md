# 🚀 Настройка автоматического деплоя на Link-Host

## Шаг 1: Подготовка сервера

1. **Убедитесь, что SSH доступен по паролю** на вашем сервере
2. **Создайте директорию для проекта**:
   ```bash
   sudo mkdir -p /var/www/your-domain.com
   sudo chown $USER:$USER /var/www/your-domain.com
   ```

## Шаг 2: Настройка GitHub Secrets

В настройках репозитория GitHub (`Settings` → `Secrets and variables` → `Actions`) добавьте:

| Secret | Описание | Пример |
|--------|----------|--------|
| `HOST` | IP адрес или домен сервера | `123.456.789.10` |
| `USERNAME` | имя пользователя на сервере | `root` или `admin` |
| `PASSWORD` | пароль от пользователя | `your_password` |
| `PORT` | порт SSH (обычно 22) | `22` |

## Шаг 3: Настройка веб-сервера

### Для Nginx:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/your-domain.com;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }
}
```

### Для Apache:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/your-domain.com
    
    <Directory /var/www/your-domain.com>
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

- **Используйте сложный пароль** для сервера
- **Ограничьте доступ** к серверу только необходимыми IP
- **Регулярно меняйте пароли**
- **Рассмотрите использование SSH ключей** для большей безопасности

## 🆘 Troubleshooting

### Ошибка подключения
- Проверьте правильность HOST, USERNAME, PASSWORD
- Убедитесь, что SSH доступен по паролю
- Проверьте настройки SSH на сервере

### Ошибка прав доступа
- Убедитесь, что пользователь имеет права sudo
- Проверьте права на директорию `/var/www/your-domain.com`

### Файлы не обновляются
- Проверьте путь в workflow (`/var/www/your-domain.com`)
- Убедитесь, что веб-сервер настроен правильно 