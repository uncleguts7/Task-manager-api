# Task Manager API

My first RESTful API using in PHP Laravel providing tasks management for personal requirement.  Key features are token based API authentication, ownership based access control with provides full-CRUD operation.

## Features

- User registration and login with hashed passwords
- Token authentication using Laravel Sanctum
- Full CRUD on Tasks, that is to create, read, update and delete them
- Ownership enforcement users cannot see or change other user items.
- Request validation with expliciterror responses
- RESTful route design following standard conventions

## API Endpoints

| Method | Endpoint | Description  | Auth Required |
|--------|----------|--------------|----------------|
| POST | /api/register | Register a new user | No |
| POST | /api/login | Login and receive a token | No |
| GET | /api/tasks | Get all tasks for the logged-in user | Yes |
| POST | /api/tasks | Create a new task | Yes |
| GET | /api/tasks/{id} | Get a single task | Yes |
| PUT | /api/tasks/{id} | Update a task | Yes |
| DELETE | /api/tasks/{id} | Delete a task | Yes |