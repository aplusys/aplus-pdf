# Snappy for Laravel 12

A simple, modern Laravel 12+ wrapper for [knplabs/knp-snappy](https://github.com/KnpLabs/snappy), allowing you to generate PDFs and Images from HTML using `wkhtmltopdf` and `wkhtmltoimage`.

## Features

- **Laravel 12+ Support**: Built for the latest version of Laravel.
- **Fluent API**: Chainable methods for customization (`setPaper`, `setOrientation`, `setMargins`).
- **View Integration**: Directly render Blade views into PDFs or Images (`loadView`).
- **Input/Output Flexibility**: 
  - Load from HTML strings, Blade Views, or existing Files.
  - Download responses, Inline responses, or Save to disk.
- **Facades**: Clean `Pdf` and `Image` facades for easy usage.

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
Download the [installer here](https://wkhtmltopdf.org/downloads.html).

## Package Installation

```bash
composer require aplus/snappy
```

### Configuration

Publish the config file to set binary paths and default options (like A4, Portrait, 0 margins).

```bash
php artisan vendor:publish --provider="Aplus\Snappy\SnappyServiceProvider"
```

## Usage

### PDF Generation

**1. Download a View:**

```php
use Aplus\Snappy\Facades\Pdf;

public function invoice($id)
{
    $order = Order::find($id);
    
    return Pdf::loadView('invoices.show', ['order' => $order])
        ->setPaper('a4')
        ->setOrientation('landscape')
        ->download('invoice.pdf');
}
```

**2. Inline Display (Open in Browser):**

```php
return Pdf::loadHTML('<h1>Usage Report</h1>')
    ->inline('report.pdf');
```

**3. Save to File:**

```php
Pdf::loadFile(public_path('reports/template.html'))
    ->save(storage_path('app/reports/daily.pdf'));
```

**4. Fluent Options:**

```php
Pdf::loadView('report')
    ->setOption('footer-right', '[page] of [toPage]')
    ->setOption('font-size', 10)
    ->download();
```

### Image Generation

Usage is identical to PDF, just use the `Image` facade.

```php
use Aplus\Snappy\Facades\Image;

return Image::loadView('charts.analytics')
    ->setOption('quality', 90)
    ->download('chart.jpg');
```

## Limitations

This package relies on `wkhtmltopdf`, which uses an older Qt WebKit rendering engine. Please be aware of the following limitations:

1.  **NO CSS Grid Support**: The engine does not support `display: grid`.
    *   *Workaround*: Use Flexbox (with `-webkit-` prefixes like `-webkit-box`), Tables, or Floats for layout.
2.  **Limited Modern CSS/JS**:
    *   Modern CSS features (e.g., CSS Variables, newer selectors) may not work or behave inconsistently.
    *   ES6+ JavaScript features are not supported.
3.  **Flexbox Quirks**: Flexbox is supported but requires the older usage syntax (often needs `-webkit-box` or `-webkit-flex`).
4.  **Performance**: Generating large/complex PDFs can be memory intensive and blocking. For high volume, consider queuing the jobs.

If you require full modern CSS support (Grid, etc.), you should look into Headless Chrome solutions (e.g., Puppeteer or Browsershot).
