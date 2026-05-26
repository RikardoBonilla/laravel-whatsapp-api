# laravel-whatsapp-api

> WhatsApp messaging API built with Laravel 12, Twilio and Domain-Driven Design.

[![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://docker.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## What it does

REST API that sends WhatsApp messages through Twilio. Built with a full DDD architecture (Domain, Application, Infrastructure, Presentation) and async message processing via Redis queues.

## Stack

- **Laravel 12** — PHP 8.3 FPM
- **Twilio** — WhatsApp channel integration
- **Redis** — Async message queue
- **MySQL 8** — Message persistence
- **Nginx** — Web server
- **Docker Compose** — Full containerized environment

## Architecture

```
Domain/WhatsApp/
├── Entities/         # Message, Conversation
├── Repositories/     # Contracts for persistence
├── Services/         # Business logic
└── ValueObjects/     # PhoneNumber, MessageContent

Application/WhatsApp/
└── UseCases/         # SendMessage, GetHistory

Infrastructure/
└── Twilio/           # Twilio adapter implementation

Presentation/
└── Http/Controllers/ # API controllers
```

## Quick Start

```bash
git clone https://github.com/RikardoBonilla/laravel-whatsapp-api.git
cd laravel-whatsapp-api

cp backend/.env.example backend/.env
# Add your Twilio credentials to .env

docker-compose up -d
docker exec chatbot_app php artisan key:generate
docker exec chatbot_app php artisan migrate
```

## API Usage

**Send a WhatsApp message:**

```bash
curl -X POST http://localhost:8000/api/whatsapp/send \
  -H "Content-Type: application/json" \
  -d '{"phone_number": "+573001234567", "content": "Hello from the API!"}'
```

**Response:**
```json
{
  "success": true,
  "message_id": "uuid-here",
  "message": "Message sent successfully"
}
```

## Environment Variables

```env
TWILIO_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
```

## Running Tests

```bash
docker exec chatbot_app php artisan test
```

---

*Built by [Ricardo Andres Bonilla Prada](https://github.com/RikardoBonilla)*
