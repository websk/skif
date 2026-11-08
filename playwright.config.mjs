import {defineConfig, devices} from '@playwright/test';

export default defineConfig({
    testDir: './tests/browser',
    timeout: 30_000,
    expect: {timeout: 5_000},
    fullyParallel: false,
    forbidOnly: Boolean(process.env.CI),
    retries: process.env.CI ? 2 : 0,
    reporter: process.env.CI ? [['line'], ['html', {open: 'never'}]] : 'line',
    use: {
        baseURL: process.env.SKIF_E2E_BASE_URL || 'https://skif.devbox',
        ignoreHTTPSErrors: true,
        screenshot: 'only-on-failure',
        trace: 'retain-on-failure',
        video: 'retain-on-failure',
    },
    projects: [
        {name: 'desktop-chromium', use: {...devices['Desktop Chrome']}},
        {name: 'mobile-chromium', use: {...devices['Pixel 7']}},
    ],
});
