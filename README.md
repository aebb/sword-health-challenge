### Technical Assessment

#### Tech Stack:
- Docker
- PHP 7.4
- Symfony 5.3
- MySQL
- Redis

#### Install and Run:

```docker-compose up -d```

Container access: ```docker exec -it task-sh /bin/bash```

##### Install dependencies

```composer update -vvv```

##### Create the database
```php bin/console doctrine:database:create```

##### Run the migrations
```php bin/console doctrine:migration:migrate```

##### Create users
```php bin/console doctrine:fixtures:load```

ROLE_TECHNICIAN TOKENS: tech1token, tech2token

ROLE_MANAGER TOKENS: manager1token

##### Run the consumer to process notifications
```php bin/console messenger:consume async -vv```

#### Tests:

##### To run the tests

```vendor/bin/phpunit -c phpunit.xml ./tests```

#### Quality Tools:

##### Run code beautifier

```vendor/bin/phpcbf```

##### Run code sniffer

```vendor/bin/phpcs```

##### Run mess detector

```vendor/bin/phpmd ./src text ./phpmd.xml```

composer.json also contains shortcuts for these commands

        "test-unit": "vendor/bin/phpunit -c phpunit.xml ./tests/Unit",
        "test-integration":"vendor/bin/phpunit -c phpunit.xml ./tests/Integration",
        "run-tests": [
            "@test-unit",
            "@test-integration"
        ],
        "phpcs": "vendor/bin/phpcs",
        "phpcbf": "vendor/bin/phpcbf",
        "phpmd": "vendor/bin/phpmd ./src text ./phpmd.xml"

#### Solution:

##### PHP:
- PSR-12 Standard

- [GET,POST] /tasks endpoints that requires role and token authentication (see config/packages/security.yaml)

- POST /tasks
```
curl --location --request POST 'http://localhost:8080/tasks' \
--header 'X-AUTH-TOKEN: tech1token' \
--header 'Content-Type: application/json' \
--data-raw '{
    "summary": "dummy1"
}'
```

- GET /tasks

Optional query parameters: start & count
```
curl --location --request GET 'http://localhost:8080/tasks?start=1&count=5' \
--header 'X-AUTH-TOKEN: tech1token'
```

##### Redis:
- When the technician creates a new task, it actually sends a message to a redis-stream. 
A consumer picks up the message and runs the process to notify all managers.
The system can be extended to support multiple notification systems but at the moment it only supports stdout notifications.
(see App/Message/ and (see config/packages/messenger.yaml))


##### Tests:
- PHPUnit for tests (code coverage ~100%):
1) Integration: In-memory SQLLite for persistence, In-memory transport for messaging system
2) Unit: PHPUnit mock objects for nearly all dependencies:


### Future work:

Create a role management system instead of the auto generated system.
Parallelize notification system with multiple workers.
Add new notification systems (sms, email, etc...)








