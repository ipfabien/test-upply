# Questions

For this assignment you also have to answer a couple of questions.
There is no correct answer and none is mandatory, if you don't know just skip it.

**What you will improve with your solution ?**

- Implement finer exception handling with custom exceptions in the Domain, Infrastructure, and UserInterface layers. For the current scope, the existing error handling is sufficient.

- Add Symfony Messenger if needed later (to handle asynchronous processing, or to use middleware for generic logic in handlers such as logging or transaction management, or for event dispatching). The current CQRS implementation does not require Messenger.

- Update tests to make them fully independent and wrap each test in a transaction to avoid polluting the test database.

- Improve the /knight endpoint to handle data volume (pagination?) and add potential filters, to be discussed with the Product team for the best decision.

- If the Taskfile grows, split it into multiple smaller files.

- Confirm with the Product team whether the Knight’s name should be unique, and whether the /knight API should or should not return the externalId.

- Improve log management (dedicated channels, centralized logging service, etc.).

- Have one controller per API endpoint for better separation of concerns.

- If the application grows significantly, refactor tests to follow the Given/When/Then structure, using traits to share data setup logic and builders to mock Value Objects, improving test readability.

- Possibly add PHPSpec for unit testing to cover internal calls across all services.

- Add events when a Command has been successfully executed, if this becomes a business need.

- Increase PHPStan to the maximum level.

- Add NelmioApiDocBundle to generate complete Swagger-based API documentation for all endpoint contracts.

- Implement JWT authentication to secure the API.

## What do you think of the initial project structure and how you will improve it?

The initial structure is already interesting and supports DDD, CQRS, and hexagonal architecture.

Use of the port/adapter concept thanks to the interface contracts already in place.

It would be good to rename Controller to UserInterface, e.g., UserInterface/Http/Controller/Api, and Handler to Application, separate read/write repositories more explicitly if features expand.

## For you, what are the boundaries of a service inside a microservice architecture?

The microservice has an isolated and autonomous business domain, so it is fully responsible for its own functionality, which makes it scalable.

On the other hand, there is a risk of data desynchronization with other microservices.

## What are the most relevant usages for SQL, NoSQL, key-value and document stores?

SQL: because it is relational.

NoSQL: for large volumes of data.

Key-value: for performance.

Document: because it uses JSON.
