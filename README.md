# Product Ordering API System

A modern product ordering system demonstrating **proper separation of layers** (Presentation, Business Logic, Data) with business rule validation, shopping cart, checkout, search, pagination, and dark mode.

## Tech Stack

- **Backend:** Laravel 12
- **Frontend:** Vue.js 3 
- **Styling:** Tailwind CSS v4
- **Database:** SQLite (default) / MySQL


## System Architecture

```
┌─────────────────────────────────────────────────┐
│              Presentation Layer                 │
│              Vue.js + Tailwind CSS              │
│          (resources/js/pages & components/)     │
└──────────────────────┬──────────────────────────┘
                       │ HTTP Requests
┌──────────────────────▼──────────────────────────┐
│            Business Logic Layer                  │
│     Controllers → OrderService (Validation)      │
│  (app/Http/Controllers/ → app/Services/)         │
└──────────────────────┬──────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────┐
│                 Data Layer                        │
│          Eloquent ORM → SQLite/MySQL              │
│           (app/Models/Product.php)                │
└─────────────────────────────────────────────────┘
```

## Guide Questions and Answers

### 1. What is business logic in software architecture?

Business logic is the set of rules that defines how the system should behave for real business scenarios. In this project, examples include checking if a product exists, rejecting invalid quantities, preventing orders beyond available stock, and updating stock only after a valid order.

### 2. Why should business logic not be placed in the UI layer?

Business logic should not live in the UI because UI checks can be bypassed using direct API requests. If rules exist only in the frontend, invalid requests can still reach the backend and affect data. Keeping rules in backend services ensures all clients follow the same constraints.

### 3. How does business logic improve data integrity?

Business logic improves data integrity by enforcing validation before writes and by controlling updates safely. In this project, order processing uses transactions and row locking to avoid race conditions and overselling, while validation ensures required fields and valid quantities.

### 4. What happens if business logic is not implemented?

Without business logic, the system may accept invalid orders, produce inconsistent stock values, and behave differently across UI and direct API clients. This leads to unreliable behavior, weak validation, and higher risk of corrupted or inconsistent data.


## API Endpoints

### GET `/api/products`

Returns the list of all products. Supports optional search.

**Query Parameters:**
- `search` (optional) — Filter products by name

**Examples:**
```
GET /api/products
GET /api/products?search=laptop
```

**Response:**
```json
[
  { "id": 1, "name": "Elden Ring", "image": "/images/products/eldenring.jpg", "price": 3490.00, "stock": 15 }
]
```

### POST `/api/order`

Places an order for a product.

**Request Body:**
```json
{
  "product_id": 1,
  "quantity": 2,
  "full_name": "Juan Dela Cruz",
  "location": "Manila City"
}
```

**Success Response (200):**
```json
{
  "result": "Stock updated",
  "message": "Order successful",
  "product": "Elden Ring",
  "remainingStock": 13
}
```

**Error Responses:**

| Scenario | Status | Response |
|---|---|---|
| Product not found | 404 | `{ "result": "Error response", "error": "Product not found" }` |
| Quantity = 0 | 400 | `{ "result": "Invalid request", "error": "Invalid quantity" }` |
| Negative quantity | 400 | `{ "result": "Error", "error": "Invalid quantity" }` |
| Stock exceeded | 400 | `{ "result": "Order rejected", "error": "Not enough stock" }` |

## Business Logic Rules

All rules are enforced in `app/Services/OrderService.php`:

1. **Product must exist** — Returns 404 if product ID is invalid
2. **Quantity must be > 0** — Returns 400 for zero or negative values
3. **Quantity cannot be negative** — Returns 400
4. **Quantity must not exceed stock** — Returns 400 if insufficient stock
5. **If stock is 0, order is rejected** — Returns 400
6. **Valid order reduces stock** — Stock is decremented by ordered quantity

## Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- npm

### Installation

```bash

# 1. Set up environment
cp .env.example .env
php artisan key:generate

# 2. Run migrations and seed the database
php artisan migrate:fresh --seed

# 3. Build frontend assets
npm run build

# 4. Start the development server
php artisan serve
```

The app will be available at `http://localhost:8000`.



## curl Testing Commands 

### Retrieve all products
```bat
curl -s -o response.json -w "HTTP Status: %{http_code}\n" http://localhost:8000/api/products & type response.json | py -m json.tool & del response.json
```

### Search products
```bat
curl -s -o response.json -w "HTTP Status: %{http_code}\n" "http://localhost:8000/api/products?search=elden" & type response.json | py -m json.tool & del response.json
```

### Place a valid order
```bat
curl -s -o response.json -w "HTTP Status: %{http_code}\n" -X POST http://localhost:8000/api/order -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"product_id\":2,\"quantity\":1,\"full_name\":\"Juan Dela Cruz\",\"location\":\"Manila City\"}" & type response.json | py -m json.tool & del response.json
```
Expected: { "result": "Stock updated", "message": "Order successful", ... } (200)

### Edge Case Tests

Invalid product ID:
```bat
curl -s -o response.json -w "HTTP Status: %{http_code}\n" -X POST http://localhost:8000/api/order -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"product_id\":999,\"quantity\":1,\"full_name\":\"Juan Dela Cruz\",\"location\":\"Manila City\"}" & type response.json | py -m json.tool & del response.json
```
Expected: { "result": "Error response", "error": "Product not found" } (404)

Quantity = 0:
```bat
curl -s -o response.json -w "HTTP Status: %{http_code}\n" -X POST http://localhost:8000/api/order -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"product_id\":1,\"quantity\":0,\"full_name\":\"Juan Dela Cruz\",\"location\":\"Manila City\"}" & type response.json | py -m json.tool & del response.json
```
Expected: { "result": "Invalid request", "error": "Invalid quantity" } (400)

Negative quantity:
```bat
curl -s -o response.json -w "HTTP Status: %{http_code}\n" -X POST http://localhost:8000/api/order -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"product_id\":1,\"quantity\":-5,\"full_name\":\"Juan Dela Cruz\",\"location\":\"Manila City\"}" & type response.json | py -m json.tool & del response.json
```
Expected: { "result": "Error", "error": "Invalid quantity" } (400)

Stock exceeded:
```bat
curl -s -o response.json -w "HTTP Status: %{http_code}\n" -X POST http://localhost:8000/api/order -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"product_id\":1,\"quantity\":9999,\"full_name\":\"Juan Dela Cruz\",\"location\":\"Manila City\"}" & type response.json | py -m json.tool & del response.json
```
Expected: { "result": "Order rejected", "error": "Not enough stock" } (400)

Order out-of-stock product (Gaming Chair, stock = 0):
```bat
curl -s -o response.json -w "HTTP Status: %{http_code}\n" -X POST http://localhost:8000/api/order -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"product_id\":16,\"quantity\":1,\"full_name\":\"Juan Dela Cruz\",\"location\":\"Manila City\"}" & type response.json | py -m json.tool & del response.json
```
Expected: { "result": "Order rejected", "error": "Not enough stock" } (400)

Missing full name:
```bat
curl -s -o response.json -w "HTTP Status: %{http_code}\n" -X POST http://localhost:8000/api/order -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"product_id\":1,\"quantity\":1,\"location\":\"Manila City\"}" & type response.json | py -m json.tool & del response.json
```
Expected: validation error response (422) for `full_name`

Missing location :
```bat
curl -s -o response.json -w "HTTP Status: %{http_code}\n" -X POST http://localhost:8000/api/order -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"product_id\":1,\"quantity\":1,\"full_name\":\"Juan Dela Cruz\"}" & type response.json | py -m json.tool & del response.json
```
Expected: validation error response (422) for `location`


## Screenshots

All required API test screenshots for this lab are stored in the `screenshots/` folder.



## Project Structure

```
ordering_system/
├── app/
│   ├── Http/Controllers/
│   │   ├── ProductController.php    # GET /api/products (with search)
│   │   └── OrderController.php      # POST /api/order
│   ├── Models/
│   │   └── Product.php              # Product model (id, name, image, price, stock)
│   └── Services/
│       └── OrderService.php         # Business logic & validation
├── database/
│   ├── migrations/
│   │   └── 2026_03_13_000000_create_products_table.php
│   └── seeders/
│       └── ProductSeeder.php        
├── public/images/products/          # Product images (PNG/JPG files)
├── resources/
│   ├── css/app.css                  # Tailwind + dark mode config
│   └── js/
│       ├── components/
│       │   ├── Navbar.vue           # Sticky navbar + dark mode toggle + cart badge
│       │   ├── ProductCard.vue      # Product card with image + add to cart
│       │   ├── CartDrawer.vue       # Sliding cart drawer
│       │   ├── CartItem.vue         # Cart item with quantity controls
│       │   └── CheckoutModal.vue    # Checkout with totals + API ordering
│       ├── composables/
│       │   ├── useCart.ts           # Cart state + formatPrice utility
│       │   └── useDarkMode.ts       # Dark mode toggle + persistence
│       └── pages/
│           └── Products.vue         # Main page (search + pagination + grid)
├── routes/
│   ├── api.php                      # API route definitions
│   └── web.php                      # Web routes
└── README.md
```