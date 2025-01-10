## About Project

The project is an API for registering and sending WhatsApp messages in bulk using the ZipWhats API.

Register in the database using a .docx or json file with a number of numbers.
Make bulk sendings using Job Worker with the numbers registered in the database.

Laravel is accessible, powerful, and provides tools required for large, robust applications.

# Installation

```bash
composer install
php artisan key:generate
```

Configure your `.env` file with your database settings.

```bash
php artisan optimize:clear
php artisan migrate
php artisan server
php artisan queue:work
```

## Routes

- POST: /api/add-bulk-number
- POST: /api/bulk-send-file

## Body Request

* POST: /api/add-bulk-number
#### Send numbers separated by commas ","

```json
{
    "name": "required|string",
    "file": "required|mimes:txt,csv,docx|max:2048"
}
```

* POST: /api/bulk-send-file
}
```json
{
    "sender": "required|string",
    "device_limit": "optional|numeric",
    "message": "required|string"
}
```
