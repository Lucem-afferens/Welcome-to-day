# 🚀 Быстрый старт деплоя (FTP)

## 1. Подготовка GitHub репозитория

```bash
# Инициализируйте git (если еще не сделано)
git init
git add .
git commit -m "Initial commit"

# Создайте репозиторий на GitHub и добавьте remote
git remote add origin https://github.com/your-username/your-repo.git
git push -u origin main
```


## 2. Настройка GitHub Secrets

В настройках репозитория (`Settings` → `Secrets and variables` → `Actions`) добавьте:

| Secret | Описание |
|--------|----------|
| `FTP_SERVER` | FTP сервер (например, ftp.your-host.ru) |
| `FTP_USERNAME` | FTP логин |
| `FTP_PASSWORD` | FTP пароль |

## 3. Настройка сервера

```bash
# Убедитесь, что папка существует на сервере
# /www/welcome-to-day.ru/ должна быть доступна через FTP
```

## 4. Тестирование

```bash
# Локальное тестирование сборки
npm run build

# Запушьте изменения
git add .
git commit -m "Add FTP deployment"
git push origin main
```

## 5. Проверка

1. Откройте GitHub Actions в вашем репозитории
2. Убедитесь, что workflow выполнился успешно
3. Откройте ваш сайт и проверьте изменения

## 🔧 Полезные команды

```bash
# Локальная разработка
npm run dev

# Сборка для продакшена
npm run build

# Предпросмотр сборки
npm run preview
```

## 📞 Поддержка

- Подробная документация: `DEPLOYMENT.md`
- Структура проекта: `README.md`
- Настройка веб-сервера: см. `DEPLOYMENT.md` 
- Все дополнительные скрипты (например, script.js) должны быть размещены в папке `public` для корректной работы на Vercel. 