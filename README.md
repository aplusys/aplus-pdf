# Snappy for Laravel 12

A simple Laravel 12+ wrapper for [knplabs/knp-snappy](https://github.com/KnpLabs/snappy), allowing you to generate PDFs and Images from HTML.

## Installation

```bash
composer require aplus/snappy
```

## Requirements

You must have `wkhtmltopdf` and `wkhtmltoimage` binaries installed on your system.

### Check Installation

```bash
wkhtmltopdf --version
wkhtmltoimage --version
```

### Installation

**Ubuntu / Debian:**

```bash
sudo apt-get install wkhtmltopdf
```

**macOS:**

```bash
brew install --cask wkhtmltopdf
```

**Windows:**

Download the installer from the [official website](https://wkhtmltopdf.org/downloads.html).

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Aplus\Snappy\SnappyServiceProvider"
```

Config file `config/snappy.php` allows you to set the binary paths and default options.

## Usage

### PDF

```php
use Aplus\Snappy\Facades\Pdf;

// Download PDF
return Pdf::loadView('invoices.show', $data)
    ->setPaper('a4')
    ->setOrientation('landscape')
    ->setMargins(10, 10, 10, 10)
    ->download('invoice.pdf');

// Inline PDF
return Pdf::loadHTML('<h1>Hello World</h1>')
    ->inline();
```

### Image

```php
use Aplus\Snappy\Facades\Image;

return Image::loadView('charts.weekly')
    ->download('chart.jpg');
```

## License

MIT
