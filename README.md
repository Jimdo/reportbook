# Reportbook

[![CircleCI](https://circleci.com/gh/Jimdo/reportbook/tree/master.svg?style=svg)](https://circleci.com/gh/Jimdo/reportbook/tree/master)

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

# Set the following variables in your .env file
MYSQL_ROOT_PASSWORD
MYSQL_DATABASE
MYSQL_USER
MYSQL_PASSWORD

# Build docker image
$ make build

# Build docker image with xdebug
$ make build-debug

# Start server
$ make server

# Make server setup
$ make setup

# Run unit tests
$ make tests
```

## General Information

The repo contains a `Makefile` to help you speeding up your development process.

```
$ make help
bootstrap       Install composer
build           Generate docker container image
build-debug     Generate docker container image with xdebug
deploy          Deploy the app to the wonderland
doc             Generate documentation
lint            Lint all the code
mongo-client    Connects to mongoDB
mysql-client    Connects to MySQL
push            Push container image to hub.docker.com
server          Start up local development web server
setup           Setup server
storage-reset   Reset local server files
tests           Execute test suite and create code coverage report
update          Update composer packages
```

## Changelog

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

### v0.3.1
  - Implement MongoDB to User and Report Service

### v0.3.2
  - Implement `ResponseObject`

### v0.3.3
  - Implement `ApplicationConfig`

### v0.3.4
  - Configure session start via controller

### v0.3.5
  - Implement TraineeId Object
  - Implement UserId Object
  - Change Namespaces

### v0.3.6
  - Create a Profile View
  - Add more Profile Data to User
  - Add more edit functions to the User and UserService
  - Add Validation to Profile View
  - Implement read only Profile View
  - Implement ProfileController

### v0.3.7
  - Add Comment
  - Add CommentRepository
  - Add CommentService
  - Add timezone to `ApplicationConfig`

### v0.3.8
  - Add CommentController
  - Implement CommentView in ViewReport

### v0.4
  - Create Event for Notification Service
  - Create Subscriber for Notification Service
  - Create Notification Service

### v0.4.1
  - Add LoggingSubscriber
  - Add Events for ReportbookService and UserService
  - Add NotificationService to UserService
  - Add NotificationService to ReportbookService

### v0.4.2
  - Add PapertrailSubscriber
  - Add PapertrailSubscriber to Controllers

### v0.4.3
  - Add MailgunSubscriber
  - Add MailgunSubscriber to Controllers for specific Events

### v0.4.4
  - Add a search function

### v0.4.5
  - Add PHPBench
  - Add Benchmarks to Project

### v0.4.6
  - Implement hashing Password Strategy
  - Soft Migration of Password Strategy for old Users

### v0.4.7
  - Add PasswordConstraints

### v0.5
  - Implement the admin user with special rights

### v0.5.1
  - Implement Category to Report

### v0.6
  - Implement Calendar Overview for Reports
  - Implement Calendar Year to Report
  - Implement sortable Report Overview

### v0.6.1
  - Implement ApplicationService

### v0.6.2
  - Implement Twig

### v0.6.3
  - Implement Medium-Editor to Report and Comment

### v0.6.4
  - Implement new design with sidebar to reportbook

### v0.7
  - Implement MySQL

### v0.7.1
  - Implement PrintService to create PDF files
  - Implement RepositoryFactory

### v0.7.2
  - Implement ApiController
  - Implement WebSerializer

### v0.7.3
  - Implement Mobile View

### v0.7.4
  - Implement NotificationService
