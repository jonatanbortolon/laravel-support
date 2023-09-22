<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

# Laravel Support

## About project

This project was made for a fullstack developer job test, when has two types of user, one can make requests and other manage this requests opened, both can comment and send atachments.

## Prerequisites
- PHP version 7.3
- Composer
- Docker
- Node.js version 16.0 or higher

## Running localy

First do 
```bash
docker compose up
```

to start database, then you need to configure env file, duplicate .env.example and rename it to .env, after update database values to

```
DB_CONNECTION=pgsql
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

now you can install php dependencies

```bash
composer install
```

and npm dependencies

```bash
npm i
# or
yarn
#or
pnpm i
```

after dependencies installed, migrate database with

```bash
php artisan migrate
```

and create storage symbolic link

```bash
php artisan storage:link
```

at least you can start Laravel and Vue server

```bash
php artisan serve

npm run watch
# or
yarn watch
#or
pnpm watch
```

Open your browser and navigate to <br/>
[http://localhost:8000](http://localhost:8000) to access the app <br/>
and [http://localhost:5050](http://localhost:5050) to access pgAdmin dashboard.