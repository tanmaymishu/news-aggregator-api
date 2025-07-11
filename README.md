# News Aggregator Backend API

## Live Demo:

https://news.tanmaydas.com

## API Docs:

https://newsapi.tanmaydas.com

## Set Up Project:

### Clone the repo:

```
git clone https://github.com/tanmaymishu/news-aggregator-api.git
```

### Environment Setup:
CD Into the repo:
```
cd news-aggregator-api
```

Copy the .env.example file to .env:
```
cp .env.example .env
```

The following environment variables are required for this app to function:

```
NEWSAPI_KEY=
NYTIMES_KEY=
THEGUARDIAN_KEY=

WWWGROUP=1000
WWWUSER=1000
```

### Port configuration:

By default the .env.example file contains the Laravel Sail environment variables for database (mysql), redis etc. using default ports. If you have local instances of mysql and redis running on port 3306 and 6379, you can either:

- stop the local services temporarilly (recommended), or
- change the host port in the docker-compose.yml file:
  
    ```diff
    - '${FORWARD_REDIS_PORT:-6379}:6379'
    - '${FORWARD_DB_PORT:-3306}:3306'
    ```
    
    ```diff
    + '${FORWARD_REDIS_PORT:-<add-a-different-port-from-local-machine>}:6379'
    + '${FORWARD_DB_PORT:-<add-a-different-port-from-local-machine>}:3306'
    ```
If you have stopped the local mysql and redis services, `docker-compose.yml` file changes won't be necessary as the ports won't conflict.

Finally, please make sure the local port 80 available, as the app will run on port 80.

### Installing Dependencies:

    docker compose build --no-cache
    docker compose exec laravel.test composer install

### Launch the app

To start the docker containers with Laravel Sail or Docker Compose in detached mode run:

    ./vendor/bin/sail up -d

or,

    docker compose up -d

The application should be running on http://localhost/

### Migrate and seed the database:

    ./vendor/bin/sail artisan migrate:fresh --seed

or,

    docker compose exec laravel.test php artisan migrate:fresh --seed

### Generate the application key:

    ./vendor/bin/sail artisan key:generate

or,

    docker compose exec laravel.test php artisan key:generate


### Clearing the cache

    ./vendor/bin/sail artisan cache:clear

or,

    docker compose exec laravel.test php artisan cache:clear

### Endpoint testing from the UI

Head over to http://localhost and an interactive API docs page will appear, where you can provide things like route parameters, query parameters, bearer token, request body, etc.
The request body is editable. The endpoints are also directly available live at: https://newsapi.tanmaydas.com

### Stop the application:

    ./vendor/bin/sail down

or,

    docker compose down

## Code Quality

### Running tests

To run tests with coverage report (minimum 80%) run the following command:

    ./vendor/bin/sail artisan test --coverage --min=80

### Static analysis

    ./vendor/bin/sail composer lint

### Type Coverage

To run tests with type coverage report (minimum 90%) run the following command:

    ./vendor/bin/sail artisan test --type-coverage --memory-limit=256M --min=90

### Format

    ./vendor/bin/sail composer format

### GitHub Actions

- GitHub actions are inside `.github/workflows` directory which are run on PR and Push on develop and main branch.

## Implementation Notes

### Caching
- All Articles are cached for 1 hour. The cache is burst whenever there are new articles fetched by the scheduler.
- Personalized article cache is burst whenever the user updates their preferences.

### SignUp/SignIn Flow
- A user can register using `POST /api/v1/register` endpoint and login using `/api/v1/login` endpoint.
- After registration and initial login, the user will be able to visit routes that do not require e-mail verification (e.g. `/api/v1/me` or `/api/v1/articles`). However, more restrictive resources (e.g. `/api/v1/own-articles`) are protected from unverified users and they will be required to verify their e-mail.
- For token generation, Laravel Sanctum is utilized. For web clients like browsers, tokens aren't required to be saved in local storage or cookie, they will be taken care of automatically. For REST-Client, Mobile phones tokens are required and will be provided one from `POST /api/v1/login`.
- The `POST /api/v1/email/verification-notification` endpoint is used to send an e-mail to the user and the e-mail will have a link pointing either to `GET ${APP_URL}/api/v1/email/verify/{id}/{hash}`, or to `GET ${APP_FRONTEND_URL}/email/verify/{id}/{hash}` for verifying the email.
- Password reset endpoints also work in similar strategy. See the API docs for the endpoints and required param, body etc.

### A Note on "Frontend Views" for e-mail verification and password reset
- For e-mail verification links, if the APP_FRONTEND_URL env variable is present, a link pointing to the frontend (e.g. nextjs) application will be sent. Otherwise, A link pointing to the backend (/api/v1/...) will be sent. In both cases, the link must be copied and pasted to a user-agent (e.g. Postman/Browser) where the user is already logged-in, either with a session, or a bearer token.
- For password reset links, if the APP_FRONTEND_URL env variable is present, the link sent in e-mail will redirect to the frontend, otherwise, it will redirect to a simple form in the backend.

### Rate Limiting
- APIs have a default rate limit applied. Excessive requests will be throttled with 429 Too Many Requests http response.

### Data Fetching
- To fetch the news articles from `NewsAPI`, `The Guardian` and `The New York Times`, there is an artisan command:

        ./vendor/bin/sail artisan news:fetch

- It takes an argument of list of sources (newsapi, nytimes, theguardian), and a `--search` option:


        ./vendor/bin/sail artisan news:fetch newsapi nytimes theguardian --search=apple

- The `news:fetch` command keeps running in the background every hour using the Laravel Scheduler. To run scheduler locally, run:


        ./vendor/bin/sail artisan schedule:work

- In Production, a cronjob should be set up like this:

        * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
  
### Deploying on Prod:
- There is also a `docker-compose.prod.yml` file for deploying the app to a live environment. Although it's not fully production-grade, but can be used for a quick glance. A version of this app is deployed at https://newsapi.tanmaydas.com on a cheapest Hetzner VM. An accompanying NextJS application is also created for consuming the APIs and deployed at https://news.tanmaydas.com using Vercel and managing the DNS through Cloudflare.
- A Caddyfile is also provided for automatic SSL certificate generation. The domain can be changed before deployment.
- Add a Dockerfile with the following content in the project root:

    ```Dockerfile
    FROM php:8.4-fpm

    # Install system dependencies and PDO MySQL extension
    RUN apt-get update && apt-get install -y \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
        && pecl install redis && docker-php-ext-enable redis

    # Optional: Install Composer globally
    COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

    WORKDIR /var/www/html
    ```

- Frontend Repo: https://github.com/tanmaymishu/news
- Live URL: https://news.tanmaydas.com
