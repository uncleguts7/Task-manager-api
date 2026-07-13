# Laravel E-commerce API

A RESTful E-Commerce backend built with Laravel, featuring token-based authentication, role-based authorization, product & category management with many-to-many relationships, cart handling, checkout with database transactions, order history, and a personal task management module.

## Features

- User registration and login with token-based authentication (Laravel Sanctum)
- Role-based authorization (admin vs. customer) enforced via custom middleware
- Ownership-based access control users can only access their own data, enforced through Laravel Policies
- Product and Category management with many-to-many relationships (admin only)
- Shopping cart with automatic quantity handling (no duplicate items)
- Checkout flow that converts a cart into an order using database transactions, with frozen pricing at time of purchase
- Order history users can view their past orders and order details
- Full request validation with clear error responses, including partial updates
- RESTful route design following standard conventions
- Task management module (personal CRUD, unrelated to store features)

## API Endpoints

| Method | Endpoint | Description | Auth Required | Admin Only |
|--------|----------|--------------|----------------|------------|
| POST | /api/register | Register a new user | No | No |
| POST | /api/login | Login and receive a token | No | No |
| GET | /api/tasks | Get all tasks for the logged-in user | Yes | No |
| POST | /api/tasks | Create a new task | Yes | No |
| GET | /api/tasks/{id} | Get a single task | Yes | No |
| PUT | /api/tasks/{id} | Update a task | Yes | No |
| DELETE | /api/tasks/{id} | Delete a task | Yes | No |
| GET | /api/categories | Get all categories | No | No |
| GET | /api/categories/{category} | Get a single category | No | No |
| POST | /api/categories | Create a category | Yes | Yes |
| PUT | /api/categories/{category} | Update a category | Yes | Yes |
| DELETE | /api/categories/{category} | Delete a category | Yes | Yes |
| GET | /api/products | Get all products | No | No |
| GET | /api/products/{product} | Get a single product | No | No |
| POST | /api/products | Create a product | Yes | Yes |
| PUT | /api/products/{product} | Update a product | Yes | Yes |
| DELETE | /api/products/{product} | Delete a product | Yes | Yes |
| POST | /api/cart/add | Add a product to the cart | Yes | No |
| GET | /api/cart | View the cart | Yes | No |
| PUT | /api/cart/items/{cartItem} | Update cart item quantity | Yes | No |
| DELETE | /api/cart/items/{cartItem} | Remove an item from the cart | Yes | No |
| POST | /api/checkout | Create an order from the cart and empty the cart | Yes | No |
| GET | /api/orders | Get order history for the logged-in user | Yes | No |
| GET | /api/orders/{order} | Get details of a single order | Yes | No |

## Authorization

- **Authentication** is handled via Laravel Sanctum protected routes require a valid bearer token.
- **Admin-only routes** (creating/updating/deleting products and categories) are protected by a custom `IsAdmin` middleware that checks the authenticated user's `role` column.
- **Ownership checks** (e.g. viewing/updating/deleting your own tasks, viewing your own orders) are handled through Laravel Policies rather than manual checks scattered across controllers.