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

### Port configuration:

By default the .env.example file contains the Laravel Sail environment variables for database (mysql), redis etc.
Please:

- either stop your local mysql/redis service if they are running on port 3306 and 6379 on your host machine, or
- change these two lines in the `docker-compose.yml` file:
  
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

    docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php84-composer:latest \
       composer install --ignore-platform-reqs

### Launch the app

To start the docker containers with Laravel Sail in detached mode run:

    ./vendor/bin/sail up -d

The application should be running on http://localhost/

### Migrate and seed the database:

    ./vendor/bin/sail artisan migrate:fresh --seed

### Generate the application key:

    ./vendor/bin/sail artisan key:generate

### Stop the application:

    ./vendor/bin/sail down

### Clearing the cache

    ./vendor/bin/sail artisan cache:clear

### Endpoint testing from the UI

Head over to http://localhost and an interactive API docs page will appear, where you can provide things like route parameters, query parameters, bearer token, request body, etc.
The request body is editable. The endpoints are also directly available live at: https://newsapi.tanmaydas.com

## Code Quality

### Running tests

- To run tests with coverage report run the following command:


    ./vendor/bin/sail artisan test --coverage

### Static analysis

    ./vendor/bin/sail composer lint

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
- The `POST /api/v1/email/verification-notification` endpoint is used to send an e-mail to the user and the e-mail will have a link pointing `GET ${APP_URL}/email/verify/{id}/{hash}` for verifying the email.
- Password reset endpoints also work in similar strategy. See the API docs for the endpoints and required param, body etc.

### Rate Limiting
- APIs have a default rate limit applied. Excessive requests will be throttled with 429 Too Many Requests http response.

### Data Fetching
- To fetch the news articles from `NewsAPI`, `The Guardian` and `The New York Times`, there is an artisan command:

    ```./vendor/bin/sail artisan news:fetch```

- It takes an argument of list of sources (newsapi, nytimes, theguardian), and a `--search` option:


    ./vendor/bin/sail artisan news:fetch newsapi nytimes theguardian --search=apple

- The `news:fetch` command keeps running in the background every hour using the Laravel Scheduler. To run scheduler locally, run:


    ./vendor/bin/sail artisan schedule:work

- In Production, a cronjob must be set up like this:

        * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
  
