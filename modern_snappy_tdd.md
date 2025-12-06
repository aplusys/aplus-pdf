# TDD: Technical Design Document — Modern Snappy

## 1. Purpose
This technical design document describes the implementation details, class diagrams, interfaces, and examples necessary to implement the features specified in the PRD for modernizing `barryvdh/laravel-snappy`.

---

## 2. Architecture Overview

High-level components:

- **PdfManager (Service / Facade)** — Central entry point resolving the configured driver and exposing the fluent API.
- **Drivers** — Implementations of `DriverInterface` for `wkhtmltopdf`, `chromium` (Browsershot), and any future drivers.
- **PdfBuilder** — Fluent builder that collects input (view/html/url), options, headers, and executes render/send operations.
- **BinaryManager** — Handles binary detection, download/installation, and verification.
- **Queue Worker Integration** — Jobs to render PDFs asynchronously.
- **Testing Fakes** — Test double for drivers and manager to avoid needing binaries in CI.
- **Artisan Commands** — `snappy:detect-binary`, `snappy:install-binary`, `snappy:verify`.

---

## 3. Package Layout (suggested)

```
src/
  Contracts/
    DriverInterface.php
    BinaryManagerInterface.php
  Drivers/
    WkhtmlDriver.php
    ChromiumDriver.php
    FakeDriver.php
  Http/
    PdfResponse.php
  Managers/
    PdfManager.php
    BinaryManager.php
  Builders/
    PdfBuilder.php
  Commands/
    DetectBinaryCommand.php
    InstallBinaryCommand.php
    VerifyCommand.php
  Support/
    Helpers.php
config/snappy.php
tests/
README.md
```

---

## 4. Interfaces

### 4.1 DriverInterface

```php
namespace Barryvdh\Snappy\Contracts;

interface DriverInterface
{
    /**
     * Render HTML to PDF and return raw bytes.
     *
     * @param string $html
     * @param array $options
     * @return string
     */
    public function render(string $html, array $options = []): string;

    /**
     * Render a URL to PDF and return raw bytes.
     */
    public function renderFromUrl(string $url, array $options = []): string;

    /**
     * Return metadata about the driver (name, version).
     */
    public function info(): array;
}
```

### 4.2 BinaryManagerInterface

```php
interface BinaryManagerInterface
{
    public function detect(string $binary): ?string;
    public function install(string $binary): bool;
    public function verify(string $binary): bool;
}
```

---

## 5. Key Classes

### 5.1 PdfManager

Responsibilities:
- Resolve the configured driver.
- Provide helper factory methods `pdf()`, `driver()`.
- Bind to service container.

Important methods:
- `driver($name = null): DriverInterface`
- `builder(): PdfBuilder`
- `fake(Closure|array $options = null)` — register fake driver for tests.

### 5.2 PdfBuilder

Fluent builder used by app code and controllers. Example capabilities:
- `view($name, $data = [])`
- `html(string $html)`
- `url(string $url)`
- `option(string $key, $value)` or `options(array)`
- `stream()` — returns `Illuminate\Http\Response` (streamed)
- `download(string $filename)` — returns download response
- `save(string $path)` — write to disk
- `send()` — send as response inline
- `queue(string $connection = null, array $jobOptions = [])` — push render job

The builder collects content source and delegates to the resolved driver for rendering.

### 5.3 Drivers

**WkhtmlDriver** — adapts `Knp\Snappy` or directly shells out to wkhtmltopdf.
- Should allow passing `stdin` or temp file.
- Provide options mapping.

**ChromiumDriver** — wraps Spatie Browsershot API and converts its output into bytes.

**FakeDriver** — returns static bytes or reads stub file for tests.

### 5.4 BinaryManager

- Implement detection heuristics:
  - Check configured `snappy.binary_path`
  - Check common system paths (`/usr/local/bin`, `/usr/bin`, `C:\Program Files\...`)
  - Use `which`/`where` on platforms
- Implement `install()` for supported OS targets by downloading prebuilt binaries into `storage/snappy/bin/{platform}`.
- Implement `verify()` by running `--version` or a dry-run.

---

## 6. Configuration

`config/snappy.php` (key options):

```php
return [
    'driver' => env('SNAPPY_DRIVER', 'wkhtmltopdf'),

    'drivers' => [
        'wkhtmltopdf' => [
            'binary' => env('WKHTMLTOPDF_BINARY', null),
            'options' => [
                'no-outline' => true,
                'margin-top' => '10mm',
            ],
        ],

        'chromium' => [
            'executable_path' => env('CHROMIUM_PATH', null),
            'options' => [],
        ],
    ],

    'temporary_path' => storage_path('framework/snappy'),
    'cache' => [
        'enabled' => true,
        'ttl' => 3600,
    ],
];
```

---

## 7. Fluent API Examples

(These code snippets are in the repo; the package also keeps backward-compatible facade methods.)

```php
// Controller example
public function downloadInvoice(PdfManager $pdf)
{
    return $pdf->builder()
        ->view('invoices.show', compact('invoice'))
        ->option('margin-top', '15mm')
        ->download("invoice-{$invoice->id}.pdf");
}

// Stream
return pdf()->view('report.large')->stream();

// Queue
pdf()->view('reports.monthly')->queue();

// Fake in tests
Pdf::fake()->returnPdf(base_path('tests/stubs/fake.pdf'));
```

---

## 8. Queue Integration

- Provide `RenderPdfJob` that accepts builder payload (source type, options, filename).
- Job resolves driver and performs render then stores result using `Storage`.
- Allow `onQueue('pdf')` and provide config defaults.

---

## 9. Caching Strategy

- Compute cache key from canonical input (source type + contents hash + sorted options).
- Use Laravel cache store (configurable) to store rendered binary bytes or path to stored file.
- Respect TTL and provide `bust` option to skip cache.

---

## 10. Testing

- Provide `Pdf::fake()` to register `FakeDriver` through service container binding.
- Tests should assert a rendered file was "generated" by checking that `Storage::disk('local')->exists()` or by asserting driver receive correct inputs.
- Provide PHPUnit helpers in `tests/TestCase.php` for common assertions.

---

## 11. Artisan Commands

- `snappy:detect-binary {driver?}` — prints detected binary path
- `snappy:install-binary {driver} {--platform=}` — downloads and installs
- `snappy:verify {driver?}` — runs quick verification

Commands use `BinaryManager` and return informative exit codes and messages.

---

## 12. Security Considerations

- Avoid exposing binary download URLs that can be hijacked — sign or pin releases.
- Sanitize HTML inputs; do not render untrusted HTML without review.
- Restrict temporary file access permissions.

---

## 13. Performance & Optimization Notes

- Prefer piping HTML via stdin where supported (less disk IO).
- For small PDFs, keep in memory; for large ones, stream to temporary file.
- Avoid spawning a process per page when possible; experiment with a worker pool or a long-running service.

---

## 14. CI/CD

- Use GitHub Actions matrix for PHP 8.1, 8.2, 8.3 and Laravel 10/11.
- Use stubs/fakes for drivers; do not require actual binaries in CI.
- Add static analysis (phpstan), tests, and code style checks.

---

## 15. Backward Compatibility Strategy

- Keep facade methods and basic helper functions working by mapping old calls to new `PdfBuilder` under the hood.
- Provide a `UPGRADE.md` documenting breaking changes and migration snippets.

---

## 16. Open Questions

1. Should we directly vendor and ship wkhtmltopdf binaries in the package (size concerns) or provide an installer only?  
2. To what extent do we support Windows for the `install` command out-of-the-box?  
3. Experiment: reuse a long-running `wkhtmltopdf` process vs. kill per request (stability vs. performance trade-offs).

---

## 17. Implementation Plan (milestones)

- Week 1: Scaffolding, interfaces, PdfManager, PdfBuilder.  
- Week 2: Implement WkhtmlDriver using `knp/snappy`, BinaryManager skeleton, config.  
- Week 3: ChromiumDriver (browsershot) + fake driver + tests.  
- Week 4: Queue job, caching, streaming, artisan commands, docs.


---

## 18. Appendix — Sample Class Stubs

_Class stubs are included in the repository under `src/` and should be used as the starting point for implementation._


---

End of TDD.

