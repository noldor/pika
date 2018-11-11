# Установка

```bash
git clone git@github.com:noldors/pika.git
cd pika
vagrant up
```

Сайт будет доступен по адресу http://192.168.10.10

# Тесты

```bash
make test # запускает phpunit
make coverage # запускает phpunit и формирует покрытие в папке coverage/coverage-html
make infection # запускает infection
```