const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

(async () => {
    // Arguments: 0=node, 1=script, 2=inputPath, 3=outputPath, 4=optionsJson
    const args = process.argv.slice(2);

    if (args.length < 2) {
        console.error('Usage: node playwright-pdf.js <input-html-file> <output-pdf-file> [options-json]');
        process.exit(1);
    }

    const inputPath = args[0];
    const outputPath = args[1];
    let options = {};

    try {
        if (args[2]) {
            options = JSON.parse(args[2]);
        }
    } catch (e) {
        console.error('Error parsing options JSON:', e);
        process.exit(1);
    }

    let browser;
    try {
        // Launch browser
        // Check if executablePath is provided in options
        const launchOptions = {};
        if (options.executablePath) {
            launchOptions.executablePath = options.executablePath;
        }

        browser = await chromium.launch(launchOptions);
        const page = await browser.newPage();

        // Read input HTML
        const htmlContent = fs.readFileSync(inputPath, 'utf8');

        // Set content with waitUntil option
        const waitUntil = options.waitUntil || 'networkidle';
        await page.setContent(htmlContent, { waitUntil: waitUntil });

        // Map options to Playwright PDF options
        // https://playwright.dev/docs/api/class-page#page-pdf
        const pdfOptions = {
            path: outputPath,
            format: options.format || 'A4',
            landscape: options.landscape || false,
            printBackground: options.printBackground !== false, // default true usually desired
            displayHeaderFooter: options.displayHeaderFooter || false,
            scale: options.scale || 1,
        };

        if (options.file_options) {
            Object.assign(pdfOptions, options.file_options);
        }

        // Handle margins
        if (options.margin) {
            pdfOptions.margin = options.margin;
        }

        // Generate PDF
        await page.pdf(pdfOptions);

    } catch (error) {
        console.error('PDF Generation Error:', error);
        process.exit(1);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
})();
