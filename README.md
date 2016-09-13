# Reportbook

[![Build Status](https://travis-ci.org/Jimdo/reportbook.svg?branch=master)](https://travis-ci.org/Jimdo/reportbook)

A report book as a service.

During the traineeship every trainee has to write regular reports.
Those have to be approved by a trainer or the person in charge of
the trainees. This software helps writing and organizing the reports of the trainees. It also simplifies the process of approval of those reports for the trainer.

## Setup

```
# Clone the repo
$ git clone git@github.com:Jimdo/reportbook.git

# Install composer and project dependencies
$ make bootstrap
```

## General Information

The repo contains a `Makefile` to help you speeding up your development process.

```
$ make help
bootstrap    Install composer
doc          Generate documentation
lint         Lint all the code
server       Start up local development web server
tests        Execute test suite and create code coverage report
update       Update composer packages
```

## Roadmap

### v0.1

  - Basic CRUD operations on the reportbook
  - Simple web interface for CRUD operations
  - Simple file system based persistence mechanism

### v0.1.1
  - Refactoring into view models
  - Add basic validation
  - Add error messages to forms
  - Add exceptions to `ReportFileRepository`

### v0.2
  - Implement simple Routing and Controller Framework

### v0.2.1
  - Improvements to the Routing and Controller Framework
  - Add `UserService` with basic CRUD operations
  - Add `UserRepository` and `UserFileRepository`

### v0.2.2
  - Add bootstrap CSS to interface

### v0.3
  - Add `UserService` to GUI
  - Initial admin user
