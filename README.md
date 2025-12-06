# Aplus PDF (Laravel Snappy Modernized)

A modernized PDF generation package for Laravel, supporting multiple drivers including **wkhtmltopdf** and **Browsershot** (Headless Chrome). This package offers a fluent API, robust binary management, and easy testing utilities.

## Features

- **Multi-Driver Support**: Switch between `wkhtmltopdf` (legacy, lightweight) and `browsershot` (modern, CSS3/JS support) easily.
- **Fluent API**: Chainable methods for building PDFs (`Pdf::view('...')->save('...')`).
- **Binary Management**: Artisan commands to automatically install and verify dependencies (`wkhtmltopdf`, `puppeteer`, `chrome`).
- **Testing Fakes**: `Pdf::fake()` for asserting PDF generation without running binaries.
- **Queue Support**: Render PDFs in the background.

## Installation

1. Install via Composer:
   ```bash
   composer require aplus/pdf
   ```

2. Publish configuration (optional but recommended):
   ```bash
   ```bash
   php artisan vendor:publish --provider="Aplus\Pdf\PdfServiceProvider" --tag="config"
   ```

## Binary Installation

This package includes a powerful command to manage the underlying binaries required for PDF generation.

### Auto-Install (Recommended)

To install the necessary binaries for your chosen driver:

```bash
# For wkhtmltopdf (Linux/Ubuntu)
php artisan snappy:install-binary wkhtmltopdf

# For Browsershot (Chrome/Puppeteer)
php artisan snappy:install-binary chromium
```

> **Note:** The `chromium` installation includes Puppeteer and a local Chrome binary. If you use `Browsershot`, you must have Node.js installed on your server.

### Manual Verification

Verify your installation:

```bash
php artisan snappy:verify /usr/local/bin/wkhtmltopdf
```

## Usage

### Basic Usage

Use the `Pdf` facade to generate PDFs from views, HTML, or URLs.

```php
use Aplus\Pdf\Facades\Pdf;

// Download a PDF from a Blade view
return Pdf::view('invoices.show', ['invoice' => $invoice])
    ->download('invoice.pdf');

// Display inline in browser
return Pdf::html('<h1>Hello World</h1>')
    ->inline('hello.pdf');

// Save to disk
Pdf::url('https://google.com')
    ->save(storage_path('app/google.pdf'));
```

### Driver Selection

You can switch drivers at runtime:

```php
Pdf::driver('browsershot')
    ->view('reports.complex-chart')
    ->save('report.pdf');

// Or change driver in the chain:
Pdf::view('invoice')
    ->driver('browsershot')
    ->save('invoice.pdf');
```

Or configure the default driver in `config/aplus-pdf.php`.

### Options

Pass driver-specific options easily:

```php
Pdf::view('document')
    ->setOption('margin-top', '20mm') // wkhtmltopdf option
    ->setOption('landscape', true)    // Browsershot option
    ->save('doc.pdf');
```

### Asynchronous Rendering

Dispatch a job to render the PDF in the background:

```php
use Aplus\Pdf\Jobs\RenderPdfJob;

RenderPdfJob::dispatch(
    'emails.order-confirmation', 
    ['order' => $order], 
    's3', 
    'invoices/order-123.pdf'
);
```

## Testing

Use `Pdf::fake()` to verify PDF generation logic without actually rendering files.

```php
use Aplus\Pdf\Facades\Pdf;

public function test_invoice_download()
{
    Pdf::fake();

    $response = $this->get('/invoice/1');

    Pdf::assertRenderedHtml('Invoice #1');
    
    // If you used view()
    // Pdf::assertRenderedHtml('...'); // PdfFake captures the rendered HTML
}
```

## Configuration

The `config/aplus-pdf.php` file allows you to configure defaults for each driver.

```php
return [
    'default' => 'wkhtmltopdf',

    'drivers' => [
        'wkhtmltopdf' => [
            'binary' => env('WKHTMLTOPDF_BINARY', '/usr/local/bin/wkhtmltopdf'),
            'options' => [],
            'timeout' => 3600,
        ],

        'browsershot' => [
            'node_binary' => env('NODE_BINARY', '/usr/bin/node'),
            'npm_binary' => env('NPM_BINARY', '/usr/bin/npm'),
            'modules_path' => base_path('node_modules'),
        ],
    ],
    // ...
];
```

## Troubleshooting

- **"Cannot find module 'puppeteer'"**: Run `php artisan snappy:install-binary chromium` or `npm install puppeteer` in your project root.
- **"wkhtmltopdf: cannot connect to X server"**: Ensure you are using the headless version (usually default in recent versions) or install `xvfb-run` wrapper.
