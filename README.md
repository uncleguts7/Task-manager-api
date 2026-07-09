# Laravel E-commerce API

A RESTful E-Commerce backend built with Laravel, featuring token based authentication, product & category management with many to many relationships, cart handling, and a personal task management module.

## Features

- User registration and login with token-based authentication (Laravel Sanctum)
- Ownership based access control users can only access their own data
- Product and Category management with many to many relationships
- Shopping cart with automatic quantity handling (no duplicate items)
- Full request validation with clear error responses
- RESTful route design following standard conventions
- Task management module (personal CRUD, unrelated to store features)

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
| GET | /api/categories | Get all Categories | No |
| GET | /api/categories/{category} | Get a single Category | No |
| GET | /api/products | Get all Products | No |
| GET | /api/products/{product} | Get a single Product | No |
| POST | /api/products | Add a product | Yes |
| POST | /api/cart/add | Add a product in the cart | Yes |
| GET | /api/cart | Showing the cart  | Yes |
| PUT | /api/cart/items/{cartItem} | Updating cart item  | Yes |
| DELETE | /api/cart/items/{cartItem} | Deleting cart item  | Yes |
| POST | /api/checkout | Create an order from the cart and empty the cart | Yes |