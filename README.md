# Babiauto API

Backend REST API for **Babiauto**, a ride-hailing app set in Abidjan, Côte d'Ivoire
(F CFA pricing, French/English copy). Built with **Laravel 13**, **MySQL**, and **Redis**.

It powers the rider flow from the design: browse vehicle classes, search destinations,
estimate fares, request a ride, get matched to the nearest driver, track them live,
ride to the destination, and rate + tip at the end.

## Stack

- **PHP 8.4 / Laravel 13**
- **MySQL** (MariaDB-compatible) — persistent store
- **Redis** — cache, queue, sessions, the driver geospatial index (`GEOSEARCH`) used
  for nearest-driver matching, and the live per-ride driver location stream
- **Laravel Sanctum** — bearer-token API auth

## Setup

```bash
composer install
cp .env.example .env          # set DB_* and REDIS_* credentials
php artisan key:generate
php artisan migrate --seed     # creates schema + seeds catalog, drivers, demo user
php artisan serve
```

The seeders create the catalogue (vehicle classes, Abidjan places), six drivers
around Cocody/Riviera, and a demo rider:

- **email:** `koffi@babiauto.ci`  **password:** `password`

## Domain model

| Model | Purpose |
|-------|---------|
| `User` | Rider account (name, phone, language, default payment) |
| `VehicleClass` | Babi Moto / Mini / Confort / XL — seats, ETA and fare parameters |
| `Driver` | Driver + vehicle details, rating, availability and last position |
| `Place` | Searchable destination catalogue (airports, hotels, neighborhoods) |
| `SavedPlace` | A rider's saved places (Maison, Bureau, …) |
| `PaymentMethod` | cash / Mobile Money / card per rider |
| `Ride` | A trip: route, fare breakdown, status, lifecycle timestamps |
| `RideRating` | Stars + tip + comment for a completed ride |

### Ride lifecycle

```
searching → accepted → arriving → arrived → in_progress → completed
     │           │          │          │
     └───────────┴──────────┴──────────┴──────────────► cancelled
```

Transitions are guarded server-side; an illegal transition returns `422`.
On request the API matches the nearest available driver of the requested class
(Redis `GEOSEARCH`, falling back to a DB haversine scan).

## API

Base path: `/api/v1`. Authenticated routes require `Authorization: Bearer <token>`.
Fares are integers in **XOF (F CFA)**.

### Public

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/auth/register` | Create account, returns token |
| `POST` | `/auth/login` | Login, returns token |
| `GET`  | `/vehicle-classes` | List vehicle classes + pricing |
| `GET`  | `/places?q=` | Search the destination catalogue |
| `POST` | `/rides/estimate` | Fare quote per class for a pickup/dropoff |

### Authenticated (rider)

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/auth/logout` | Revoke current token |
| `GET`  | `/auth/me` | Current user |
| `GET` / `PATCH` | `/profile` | View / update profile |
| `GET` / `POST` | `/saved-places` | List / add saved places |
| `DELETE` | `/saved-places/{id}` | Remove a saved place |
| `GET` / `POST` | `/payment-methods` | List / add payment methods |
| `DELETE` | `/payment-methods/{id}` | Remove a payment method |
| `GET`  | `/rides` | Ride history (paginated) |
| `POST` | `/rides` | Request a ride (auto-matches a driver) |
| `GET`  | `/rides/{id}` | Ride detail (+ live location while active) |
| `GET`  | `/rides/{id}/tracking` | Live driver position for the ride |
| `POST` | `/rides/{id}/cancel` | Cancel a ride |
| `POST` | `/rides/{id}/rate` | Rate (1–5) + optional tip |

### Driver / dispatch lifecycle

These advance a ride through its states and stream driver position. The Babiauto
design ships a rider app only, so they are exposed under the same auth for an
end-to-end flow; in production they would sit behind a dedicated driver guard.

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/rides/{id}/arriving` | Driver en route to pickup |
| `POST` | `/rides/{id}/arrived` | Driver at pickup |
| `POST` | `/rides/{id}/start` | Trip started |
| `POST` | `/rides/{id}/complete` | Trip finished (frees driver, +1 trip) |
| `POST` | `/rides/{id}/driver-location` | Push `{lat,lng,heading}` to the live stream |

### Example: request a ride

```bash
curl -X POST /api/v1/rides \
  -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{
    "vehicle_class": "confort",
    "payment_type": "mobile",
    "pickup":  {"name":"Cocody · Riviera Golf","lat":5.3560,"lng":-3.9870},
    "dropoff": {"name":"Aéroport Félix-Houphouët-Boigny","lat":5.2614,"lng":-3.9263}
  }'
```

## Tests

```bash
php artisan test
```

Feature tests run on in-memory SQLite and degrade gracefully without Redis
(matching falls back to the DB), so the suite is self-contained.
