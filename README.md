# Upply - PHP assignment

This is the repository with the PHP assignment of **Upply**.

Read the content of this **README.md** carefully as it will help you to set up the project
for this assignment and to submit your solution.

In the assignment, you will have to contribute a micro API with PHP and Symfony framework.

## Getting started

### Requirements

The project needs few elements:

 * **PHP** >= 8.2
 * **Git**
 * **Docker**
 * any **SQL database** (PostgresSQL, MySQL, etc.)

### Project setup

This repository uses git, you should clone it on your machine.

After that your ready to go!

## It's up to you now!

### Instructions

Now please read the [INSTRUCTIONS.md](./INSTRUCTIONS.md) file located at the root of the project.
It contains all the instructions and hints for the project.

You can of course do several commits during the test to save your work. We'll look at the final result only.

### Questions

After that, read the [QUESTIONS.md](./QUESTIONS.md) file located at the root of the project.
It contains some questions about the project and more.

### Submissions

You can write some notes in [NOTES.md](./NOTES.md)

To submit your solution, you can simply push your work into a repository of your choice that we can access.

## How you will be evaluated

### Tests

Several tests are already wrote under `tests` folder.

Those tests will be executed to see if your solution works.

You can add more tests but justify if you modify any existent tests.

### Code review

Your code will also be reviewed.

### Procedure how we test

 - We will clone your sources.
 - We will run `composer install`.
 - If needed, run `docker-compose up -d`.
 - We will run test like mention in [INSTRUCTIONS.md](./INSTRUCTIONS.md) or something else if specified.
 - We will run docker to build an image `docker build . -t php-assignment`

### About the results

The result is important but there is no unique solution, there are a various way to pass the tests successfully. 
We prefer a not so perfect solution than no solution at all even if you have to change the structure of the project.

**Don't forget that you will have to explain and justify your choices during an interview.
This exercise is mainly a way to provide discussion material for it.**
