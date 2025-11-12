# Notes

Prerequisites

- PHP >= 8.2
- Composer
- Docker (and Docker Compose)

Environment files (to create at project root)

.env

```
APP_ENV=dev
APP_DEBUG=1
DATABASE_URL="pgsql://app:app@127.0.0.1:5433/upply"
```

.env.test

```
APP_ENV=test
APP_DEBUG=1
DATABASE_URL="pgsql://app:app@127.0.0.1:5433/upply_test"
```

Run the project tests (exact order used by reviewers)

```
docker compose up -d
docker build . -t php-assignment
./bin/phpunit
```

## Analysis

The `composer.json` requires a minimum PHP version of 8.2 and locks Symfony at 7.2, and there is no requirement to upgrade beyond this version for the task.

The `INSTRUCTIONS.md` file serves as the specification, so I will follow what is requested in this file, even if it means modifying the tests when they do not comply with the spec.
Three commands will be used to run and test the project: `docker-compose up -d`, `docker build . -t php-assignment`, and `./bin/phpunit`. The development will ensure compliance with these commands.
Therefore, a patch was added to execute composer install within PHPUnit’s bootstrap.
This should not be done in a real-world scenario — it is included only for the purpose of this exercise to comply with the specifications.

A micro API is requested, so I will only adhere to the constraints in the spec regarding responses and JSON usage, without implementing a full REST API with tokens, which would be heavier in development, in order to respect the YAGNI principle.

## Stack

- Docker
- PHP 8.3 (minimum required by composer.json is 8.2)
- Symfony 7.2 (locked by composer.json)
- Doctrine DBAL (ORM not necessary) with a PostgreSQL database for persistence

## Architecture

I am keeping the existing structure because the requirement states: `your implementation should be compliant with the tests already written`.
Changing the structure would require too many modifications to the tests.

The current structure allows the implementation of a hexagonal architecture, with only some naming changes:

- Controller becomes UserInterface
- Domain is already present
- Handler becomes Application for the use cases
- Infrastructure will be added

Respect for these layers will be maintained, along with the Port/Adapter concept and dependency injection.

I will introduce a lightweight CQRS, keeping the separation between read and write models to provide a more scalable base.

I will also apply DDD concepts to have a business-centric approach and because the current structure is already oriented toward its use.

Overall, I will respect SOLID principles and YAGNI as much as possible to deliver a maintainable, testable, and professional project, avoiding development that might never be used, while keeping a solid and robust foundation.

## Code Quality

Use php-cs-fixer for code formatting.
Use PHPStan for static code analysis and quality checks.
Use Deptrac to enforce hexagonal architecture compliance.

## Api

How to use with line command:

```
    curl -i -H "Content-Type: application/json" \
      -d '{"name":"Fake name","strength":10,"weapon_power":20}' \
      http://127.0.0.1:8000/knight
```

```
    curl -i http://127.0.0.1:8000/knight/e0799078-2ad5-4fdd-955f-94fbb23c64d0
```

```
    curl -i http://127.0.0.1:8000/knight
```

- DTO and Value Resolver
We use the Value Resolver to retrieve data from the request and automatically normalize it into a DTO.

- Error Handling
API routes now have an ApiEndpoint attribute, which is caught in a subscriber to return custom exceptions already handled, in JSON format.

## Issues and Choices

1) Modified on the existing code: getID in Knight instead of getId as required by the spec — typo issue.

2) Not necessary (YAGNI):

- No Messenger bus for now since everything is synchronous and there are no events.
- No complex mapping/serializer needed; light normalization is sufficient.
- No ORM; using DBAL is enough. PDO could have worked, but Doctrine DBAL allows using Doctrine migrations.

3) `Knight`: No uniqueness constraint was specified in the spec (e.g., for the name), so I assumed it’s intentional to allow two Knights with the same name.
This requires returning the externalId in KnightController::save, meaning the controller is responsible for generating the externalId, which is not ideal. Moving this to the Infrastructure layer would require the CommandHandler to return it, but it must remain void.

4) `KnightProviderRepository::getAll()` returns a `KnightSet` for strong typing.
For this exercise, I added a limit of 50 elements in the absence of a spec.
The API should ideally be enhanced with pagination and a `KnightFilter` for `getAll()` to handle this.
For large data volumes, we could also use iterateAssociative to stream results and make `KnightSet` yield data.
For this exercise, I opted for YAGNI, keeping it simple while allowing future improvements.

5) Use of the `Clock` component for dates instead of DateTimeImmutable because it facilitates mocking in tests.

## Tests

1) `KnightControllerTest`

`KnightControllerTest::testPostKnightBadData` only tests sending strength and weaponPower together, but not one without the other.
This forces a required field without specifying which one, and it does not test whether name is mandatory, even though it should implicitly be.
The spec does not clarify uniqueness either.
The chosen solution was to make the test pass by making all three properties required without any uniqueness constraint.
The best approach would be to discuss this with the Product team to make the most appropriate decision.

2) `ArenaTest`

`ArenaTest::testFight` fails because it does not respect `Arena::fight`, which correctly follows the spec.
Solution: correct the test.

3) Test dependencies

Some tests depend on previous ones, causing errors.
The chosen solution was not to modify existing tests and to truncate tables before each test file.

4) Additional tests

Added tests by taking advantage of the architectural choices that make the application more testable.

## Possible Improvements

Move to `QUESTIONS.md`
