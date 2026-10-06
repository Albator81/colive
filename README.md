# CoLive

**A housing platform for work-study students** — find a room, a studio or a flat-share close to both your school and your company, book it, and talk to the host, all in one place.

Built with **Symfony 7.3**, **API Platform**, **Doctrine / MySQL** and **Mercure** for real-time notifications.

![CoLive home page](docs/screenshots/home.jpg)

> The interface is in French: the app targets students in *alternance* (the French work-study scheme), who often need a second home for part of the year.

## Features

- **Search & filters** — browse listings by city, housing type, dates, price range, surface and equipment. Results are fetched live from a REST endpoint exposed with API Platform filters.
- **Listings** — hosts publish listings with photos, equipment tags (with autocomplete and custom tags) and availability dates; each listing has a photo carousel, reviews and an OpenStreetMap location.
- **Booking** — pick dates on an availability calendar that blocks already-reserved periods; the request lands in the host's inbox, who accepts or refuses it in one click.
- **Messaging** — contact list, one-to-one conversations and file attachments, with booking requests embedded directly in the thread.
- **Real-time notifications** — new messages, booking updates, likes and reviews are pushed to the browser over Server-Sent Events through a Mercure hub.
- **Reviews & favourites** — rate a listing, read a host's reviews, and save listings to your profile.
- **Moderation** — administrators review pending listings from a dashboard, where they can validate or reject them and remove inappropriate reviews.
- **FAQ assistant** — a small chatbot backed by the Mistral AI API answers questions about the platform.
- **Accounts** — registration, login, profile editing with avatar, role-based access control and CSRF protection.

## Screenshots

| Browse and filter listings | Listing page |
| :---: | :---: |
| ![Listings](docs/screenshots/listings.jpg) | ![Listing](docs/screenshots/listing.jpg) |
| **Booking calendar** | **Messaging with an embedded booking request** |
| ![Booking](docs/screenshots/booking.jpg) | ![Messaging](docs/screenshots/messaging.png) |
| **Profile, bookings and favourites** | **Admin moderation dashboard** |
| ![Profile](docs/screenshots/profile.png) | ![Admin](docs/screenshots/admin.jpg) |

## Tech stack

| Layer | Tools |
| :--- | :--- |
| Backend | PHP 8.2+, Symfony 7.3, Doctrine ORM, Symfony Security, Symfony Forms |
| API | API Platform 4 (search, date and range filters) |
| Database | MySQL 8, Doctrine Migrations |
| Real time | Mercure (Server-Sent Events) |
| Frontend | Twig, Bootstrap 5, vanilla JavaScript, Stimulus / Turbo, AssetMapper (no Node build step) |
| UI libraries | Leaflet + OpenStreetMap, Flatpickr, Tom Select |
| AI | Mistral AI chat completions API |
| Quality | Codeception functional tests, PHP-CS-Fixer, Foundry factories and fixtures |
| Ops | Docker Compose with FrankenPHP (based on symfony-docker) |

## Data model

```mermaid
erDiagram
    USER ||--o{ ANNOUNCE : publishes
    USER ||--o{ RESERVATION : books
    USER ||--o{ REVIEW : writes
    USER ||--o{ USER_LIKES : saves
    USER ||--o{ NOTIFICATION : receives
    USER ||--o{ MESSAGE : sends
    USER }o--o{ USER : "has as contact"
    ANNOUNCE ||--o{ ANNOUNCE_PICTURE : has
    ANNOUNCE ||--o{ RESERVATION : "is booked by"
    ANNOUNCE ||--o{ REVIEW : "is rated by"
    ANNOUNCE ||--o{ USER_LIKES : "is saved in"
    ANNOUNCE }o--o{ EQUIPMENT : offers
    ANNOUNCE }o--o{ USER_EQUIPMENT : offers
```

The code follows the standard Symfony layout: controllers in `src/Controller`, entities and repositories in `src/Entity` and `src/Repository`, Doctrine event listeners that emit notifications in `src/EventListener`, and one JavaScript file per page in `assets/js`.

## Getting started

### Requirements

- PHP 8.2 or newer with the `intl`, `pdo_mysql`, `mbstring`, `openssl` and `curl` extensions
- [Composer](https://getcomposer.org/)
- MySQL 8
- [Symfony CLI](https://symfony.com/download) for the local web server
- Optional: a [Mercure hub](https://mercure.rocks/docs/hub/install) for real-time notifications, and a [Mistral AI](https://console.mistral.ai/) API key for the assistant

### Installation

```bash
git clone https://github.com/Albator81/colive.git
cd colive
composer install
```

Create a `.env.local` file with your own settings:

```env
DATABASE_URL="mysql://USER:PASSWORD@127.0.0.1:3306/colive?serverVersion=8.0&charset=utf8mb4"

# Optional
MERCURE_URL=http://127.0.0.1:3000/.well-known/mercure
MERCURE_PUBLIC_URL=http://127.0.0.1:3000/.well-known/mercure
MISTRAL_API_KEY=your-key
```

Create the database, run the migrations and load the demo data, then start the server:

```bash
composer db
composer start
```

The app is now available at <http://localhost:8000>.

### Demo accounts

The fixtures create 20 listings with photos, reviews and favourites, plus these accounts:

| Role | Email | Password |
| :--- | :--- | :--- |
| Administrator | `admin@colive.com` | `admin` |
| Student | `test@example.com` | `password` |

The moderation dashboard is at `/admin/dashboard`.

### Docker

The repository also ships the [symfony-docker](https://github.com/dunglas/symfony-docker) setup (`compose.yaml`): FrankenPHP with a built-in Mercure hub, MySQL and Mailpit.

```bash
docker compose up --build --wait
```

### Tests and code style

```bash
composer test:codeception   # functional tests (needs a `colive_test` database)
composer test:cs            # coding standards check
composer fix:cs             # apply coding standards
```

## About the project

CoLive was built as a team project during the second year of the BUT Informatique at the IUT de Reims (2025–2026), using feature branches and merge requests on the school's GitLab.

**Team:** Hugo Alfredo, Adam Benahmed, Eliott Betry, Nathan Buffet.

Sample listing photos come from [Unsplash](https://unsplash.com) (see `src/DataFixtures/images/CREDITS.md`).
