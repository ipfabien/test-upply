# Instructions

This assignment aims to implement a micro API to simulate a fantasy themed mini-game service.

Read all the instructions carefully before beginning to code.

## Context

This mini-game is very simple.

There is an Arena where duels are hold.

 - An arena can register 2 fighters.
 - A fighter has different stats which results in a Power Level
 - The fighter with the higher Power Level wins the duel.

## Data model

We will now explain in details the data model.

### Fighter

The Fighter interface describes what can fight.

There are 2 methods in Fighter:

 - getId() returns a String which represents the unique ID of the combatant
 - getPower() returns a Float which represents the Power level of the Fighter
 
### Knight

The Knight is an entity that can be stored in the database.
 
The Knight has 2 specific attributes:
 
 - the strength (Integer) which represents the strength of the Knight
 - the weaponPower (Integer) which represents the power of the weapon wielded by the Knight

The Knight is a fighter. As such it implements the Fighter interface.
 
The Power level of a Knight can be obtained by adding its strength and its weaponPower

## Arena

This logic is only here to simulate a duel between 2 fighters.

It is a simple service with a unique method called fight which takes 2 fighters and made them fight by comparing their Power Level.

The method should return the winner or return a `null` value if the duel is a draw (the 2 combatants have the same Power Level)

In this little test, only the Knights are able to fight, but the Arena's duels should stay generic.

We can imagine for example other classes like Trolls or Goblins (which are obviously not humans) fighting in the arena too.

## API

This mini-game simulator should be designed as an API.

Endpoints needed are:
 - POST `/knight` Create a knight
 - GET `/knight` Get the list of knights
 - GET `/knight/{id}` Get one knight
 
All the content exchanges with the API should be done in JSON.
 
All the endpoints should have the same behaviour with status codes sent back to the client :

 - `200` status code when the request has been successful
 - `201` status code when the request has been fulfilled and resulted in a new resource has being created
 - `400` status code when the request is wrong (bad parameters, not a JSON payload etc...)
 - `404` status code when the resource is unavailable or does not exist

You can check the tests written for the assignment to have examples of expected status codes from a request.

## Project skeleton

The project is not empty by default. You will find a structure and several files already written for you based on symfony new project skeleton:

 - `src/Controller/*` Route implementations
 - `src/Domain/*` Domain logic and fighter models
 - `src/Handler/*` Use cases

Some parts can be wrong or missing, you can of course fix/refactor it and explain why (there is no expected choices/implementation, depends on your vision and experience).

This structure is not mandatory to succeed the assignment, you are free to change it if you feel more comfortable.
But try to keep similar features and explain the reason of your decision for any major change.

## Docker

The project will need have a `Dockerfile` to build and able to be deployed.

## What you have to do

For the API to work and for the tests to pass, you need to:

 - correctly implement the resources following the data model explained previously.
 - build **routes** to interact with these resources under `src/Controller/`.
 - setup a **database** (of your choice but preferably SQL)(with any library or none) to store these resources.
 - write **integration tests** of your database implementation under `tests/`.
 - as it's an API, you should return the correct responses depending on the requests.
 - your implementation should be compliant with the tests already written.
 - write a `Dockerfile` to build an image to run the application with Docker

## Note about the testing phase

By default, tests will be executed like:

    ./bin/phpunit

If your solution needs any database instance, you can (of your choice):
 - Add a docker compose file
 - In other case, please mention instructions in [NOTES.md](./NOTES.md) how to set up the database for testing.
