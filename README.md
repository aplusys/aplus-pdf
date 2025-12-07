# Aplus PDF (Laravel Snappy Modernized)

A modernized PDF generation package for Laravel, supporting multiple drivers including **wkhtmltopdf** and **Browsershot** (Headless Chrome). This package offers a fluent API, robust binary management, and easy testing utilities.

## Features

- **Multi-Driver Support**: Switch between `wkhtmltopdf` (legacy), `browsershot` (Puppeteer), and **Playwright** (Modern, Reliable) easily.
- **Fluent API**: Chainable methods for building PDFs (`Apdf::view('...')->save('...')`).
- **Binary Management**: Artisan commands to automatically install and verify dependencies (`wkhtmltopdf`, `puppeteer`, `chrome`).
- **Testing Fakes**: `Apdf::fake()` for asserting PDF generation without running binaries.
- **Queue Support**: Render PDFs in the background.

## Installation

1. Install via Composer:
   ```bash
   composer require aplusy/pdf
   ```

2. Publish configuration (optional but recommended):
   ```bash
   php artisan vendor:publish --provider="Aplus\Pdf\PdfServiceProvider" --tag="config"
   ```

## Binary Installation

This package includes a powerful command to manage the underlying binaries required for PDF generation.

### Auto-Install (Recommended)

To install the necessary binaries for your chosen driver:

```bash
# For wkhtmltopdf (Linux/Ubuntu)
php artisan pdf:install-binary wkhtmltopdf

# For Browsershot (Chrome/Puppeteer)
php artisan pdf:install-binary chromium

# For Playwright
php artisan pdf:install-binary playwright
```

> **Note:** The `chromium` installation includes Puppeteer and a local Chrome binary. If you use `Browsershot`, you must have Node.js installed on your server.

### Manual Verification

Verify your installation:

```bash
php artisan pdf:verify /usr/local/bin/wkhtmltopdf
```

## Usage

### Basic Usage

Use the `Apdf` facade to generate PDFs from views, HTML, or URLs.

```php
use Aplus\Pdf\Facades\Apdf;

// Download a PDF from a Blade view
return Apdf::view('invoices.show', ['invoice' => $invoice])
    ->download('invoice.pdf');

// Display inline in browser
return Apdf::html('<h1>Hello World</h1>')
    ->inline('hello.pdf');

// Save to disk
Apdf::url('https://google.com')
    ->save(storage_path('app/google.pdf'));
```

### Driver Selection

You can switch drivers at runtime:

```php
Apdf::driver('browsershot')
    ->view('reports.complex-chart')
    ->save('report.pdf');

// Or change driver in the chain:
Apdf::view('invoice')
    ->driver('browsershot')
    ->save('invoice.pdf');
```

Or configure the default driver in `config/aplus-pdf.php`.

### Options

Pass driver-specific options easily:

```php
Apdf::view('document')
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

### Zero Margins

To achieve true zero margins, you must ensure two things:
1. Set the PDF driver margins to `0` (or `'0mm'`).
2. Remove the default browser margin from your HTML/Blade view using CSS.

```html
<style>
    body {
        margin: 0;
        padding: 0;
    }
</style>
```

If you still see white space, ensure `disable-smart-shrinking` is enabled in `config/aplus-pdf.php`.

## Testing

Use `Apdf::fake()` to verify PDF generation logic without actually rendering files.

```php
use Aplus\Pdf\Facades\Apdf;

public function test_invoice_download()
{
    Apdf::fake();

    $response = $this->get('/invoice/1');

    Apdf::assertRenderedHtml('Invoice #1');
    
    // If you used view()
    // Apdf::assertRenderedHtml('...'); // PdfFake captures the rendered HTML
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

        'playwright' => [
            'node_binary' => env('NODE_BINARY', '/usr/bin/node'),
            'npm_binary' => env('NPM_BINARY', '/usr/bin/npm'),
            'timeout' => 60,
        ],
    ],
    // ...
];
```

## Troubleshooting

- **"Cannot find module 'puppeteer'"**: Run `php artisan pdf:install-binary chromium` or `npm install puppeteer` in your project root.
- **"wkhtmltopdf: cannot connect to X server"**: Ensure you are using the headless version (usually default in recent versions) or install `xvfb-run` wrapper.
