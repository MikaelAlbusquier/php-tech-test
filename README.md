# Tech test

## How to install
- composer install
- Run ./init.sh
- To add amount you can run : curl 127.0.0.1:8000/box/add/10
- To get total added so far you can run : curl 127.0.0.1:8000/box/total
- To get the history of amount added so far you can run : curl 127.0.0.1:8000/box/history

## Scenario
We want to buy an iPhone X, and we are saving our spare money to do so. We have a box at home where we put our spare change every day and need a way to track this (in a geek way).

## Requirements

- Write a REST API that allows you to:
  - Add an amount to the balance of the box.
  - Retrieve the total balance.
  - Retrieve a history of the transactions.
- Use Symfony 4 + Flex for the API.
- Use a local development environment, such as PHPs built in server, Symfony server, Vagrant, or Docker for providing access to the API.
- Use a local storage technique (CSV, SQLite, …) to save the data in the event store.

## Valued aspects
We will evaluate different aspects of the application. Such as
- The architecture chosen to solve the problem.
- Specific **Event Sourcing** domain terminology used (For example, the use of _Projections_).
- Usage (or not) of CQRS design pattern.
- Usage (or not) of an Event Bus (Event driven design).
- Commit history and organisation.
- Clean and legibility of the code.
- Algorithm complexity used to solve the problem. (Big O)

Don't overcomplicate yourself. Maybe a simpler approach, is more elegant and easier to understand and read. The aspects that say _or not_ are only there to hint you where you can move towards.

### Extra information

Valuable resources of information for the task.
- https://github.com/prooph/event-store/blob/master/docs/projections.md
- https://martinfowler.com/eaaDev/EventSourcing.html
- https://martinfowler.com/bliki/CQRS.html
- http://fideloper.com/hexagonal-architecture
- Google is your friend
