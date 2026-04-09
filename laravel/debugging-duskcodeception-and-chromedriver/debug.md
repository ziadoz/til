Start ChromeDriver with logging enabled:

```
/usr/bin/chromedriver --url-base=/wd/hub --allowed-ips="" --port=9515 --log-level=INFO --log-path=/tmp/chromedriver.log
```

Run Dusk/Codeception:

```
php artisan dusk
```

Copy logs from Docker:

```
docker-compose cp chrome:/tmp/chromedriver.log chromedriver.log
```