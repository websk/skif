import {expect, test} from '@playwright/test';

function observeFrontendFailures(page) {
    const failures = [];

    page.on('console', (message) => {
        if (message.type() === 'error') {
            failures.push(`console: ${message.text()}`);
        }
    });
    page.on('pageerror', (error) => failures.push(`pageerror: ${error.message}`));
    page.on('response', (response) => {
        const request = response.request();
        if (response.status() >= 400 && ['document', 'script', 'stylesheet', 'font', 'image'].includes(request.resourceType())) {
            failures.push(`${response.status()} ${request.resourceType()}: ${response.url()}`);
        }
    });

    return failures;
}

async function logIn(page) {
    await page.goto('/admin');
    await page.getByLabel('Email').fill(process.env.SKIF_E2E_EMAIL);
    await page.getByLabel('Пароль').fill(process.env.SKIF_E2E_PASSWORD);
    await page.getByRole('button', {name: 'Войти'}).click();
    await expect(page.locator('.app-wrapper')).toBeVisible();
}

test('HTTP file manager preserves the secure administrator session', async ({page, baseURL}) => {
    test.skip(!process.env.SKIF_E2E_EMAIL || !process.env.SKIF_E2E_PASSWORD,
        'Set SKIF_E2E_EMAIL and SKIF_E2E_PASSWORD.');
    const failures = observeFrontendFailures(page);
    await logIn(page);
    const url = new URL('/assets/1/libraries/filemanager/index.html?expandedFolder=images%2Fcontent', baseURL);
    url.protocol = 'http:';
    const folderResponse = page.waitForResponse((response) => (
        new URL(response.url()).pathname === '/filemanager'
        && new URL(response.url()).searchParams.get('mode') === 'readfolder'
        && new URL(response.url()).searchParams.get('path') === '/images/content/'
    ));
    await page.goto(url.href);
    expect((await folderResponse).status()).toBe(200);
    expect(new URL(page.url()).protocol).toBe('https:');
    expect(new URL(page.url()).searchParams.get('expandedFolder')).toBe('images/content');
    expect(failures).toEqual([]);
});

test('file manager inserts a loadable image into the content editor', async ({page}) => {
    test.skip(!process.env.SKIF_E2E_EMAIL || !process.env.SKIF_E2E_PASSWORD,
        'Set SKIF_E2E_EMAIL and SKIF_E2E_PASSWORD.');
    const failures = observeFrontendFailures(page);
    await logIn(page);
    await page.goto(process.env.SKIF_E2E_CONTENT_EDIT_URL || '/admin/content/page/3');
    const popupPromise = page.waitForEvent('popup');
    await page.getByRole('button', {name: 'Выбрать изображение', exact: true}).last().click();
    const popup = await popupPromise;
    await popup.locator('p').filter({hasText: /^kss.jpg$/}).dblclick();
    await expect.poll(() => page.evaluate(() => [...document.querySelectorAll('iframe')].some((frame) => (
        [...frame.contentDocument.querySelectorAll('img')].some((image) => (
            image.src === location.origin + '/files/images/content/kss.jpg' && image.naturalWidth > 0
        ))
    )))).toBe(true);
    await expect.poll(() => page.locator('textarea[data-skif-rich-text-editor]').last().inputValue())
        .toContain('/files/images/content/kss.jpg');
    expect(failures).toEqual([]);
});

test('admin login page has no console or asset failures', async ({page}) => {
    const failures = observeFrontendFailures(page);
    const response = await page.goto('/admin');

    expect(response?.status()).toBe(200);
    await expect(page.getByRole('heading', {name: 'Вход в систему управления'})).toBeVisible();
    await expect(page.getByLabel('Email')).toHaveAttribute('type', 'email');
    await expect(page.getByLabel('Пароль')).toHaveAttribute('type', 'password');
    await expect(page.locator('.login-box .card')).toHaveClass(/card-primary/);
    await expect(page.locator('.login-box .card')).toHaveClass(/card-outline/);
    await expect(page.locator('link[href*="bootstrap.min.css"]')).toHaveCount(1);
    await expect(page.locator('link[href*="admin-lte/css/AdminLTE.min.css"]')).toHaveCount(1);
    expect(failures).toEqual([]);
});

test('admin URL with trailing slash opens the login page', async ({page}) => {
    const response = await page.goto('/admin/');

    expect(response?.status()).toBe(200);
    await expect(page.getByRole('heading', {name: 'Вход в систему управления'})).toBeVisible();
});

test('public layout is responsive and has no console or asset failures', async ({page}) => {
    const failures = observeFrontendFailures(page);
    const response = await page.goto('/');

    expect(response?.status()).toBe(200);
    await expect(page.locator('#html.container')).toBeVisible();
    await expect(page.locator('link[href*="admin-lte/css/AdminLTE.min.css"]')).toHaveCount(1);
    await expect(page.locator('#content > .card')).toHaveClass(/card-primary/);
    await expect(page.locator('#content > .card')).toHaveClass(/card-outline/);
    const overflowingElements = await page.evaluate(() => {
        const viewportWidth = document.documentElement.clientWidth;
        return [...document.querySelectorAll('body *')]
            .filter((element) => {
                const rect = element.getBoundingClientRect();
                if (rect.width <= 1 && rect.height <= 1) {
                    return false;
                }
                return rect.right > viewportWidth + 1 || rect.left < -1;
            })
            .map((element) => `${element.tagName.toLowerCase()}#${element.id}.${element.className}`)
            .slice(0, 20);
    });
    expect(overflowingElements).toEqual([]);
    expect(failures).toEqual([]);
});

test('news menu highlights one separated item', async ({page}) => {
    const failures = observeFrontendFailures(page);
    const response = await page.goto('/news');

    expect(response?.status()).toBe(200);
    const menu = page.locator('#sidebar .nav-promote').first();
    const activeLinks = menu.locator('.nav-link.active');
    await expect(activeLinks).toHaveCount(1);
    await expect(activeLinks).toHaveText('Новости');
    await expect(menu.getByRole('link', {name: 'Главная'})).not.toHaveClass(/active/);
    const contentHeader = page.locator('#content .app-content-header');
    await expect(contentHeader.getByRole('heading', {name: 'Новости и объявления'})).toBeVisible();
    await expect(contentHeader.getByRole('navigation', {name: 'Хлебные крошки'})).toBeVisible();
    await expect(contentHeader.locator('.breadcrumb')).toHaveCSS('margin-bottom', '0px');
    await expect(page.locator('#sidebar > .card')).toHaveClass(/card-primary/);

    const linkBoxes = await menu.locator('.nav-link').evaluateAll((links) => links.map((link) => {
        const box = link.getBoundingClientRect();
        return {top: box.top, bottom: box.bottom};
    }));
    for (let index = 1; index < linkBoxes.length; index += 1) {
        expect(linkBoxes[index].top - linkBoxes[index - 1].bottom).toBeGreaterThanOrEqual(4);
    }
    expect(failures).toEqual([]);
});

test('authenticated admin shell, sidebar and menus work', async ({page}) => {
    test.skip(!process.env.SKIF_E2E_EMAIL || !process.env.SKIF_E2E_PASSWORD, 'Set SKIF_E2E_EMAIL and SKIF_E2E_PASSWORD.');
    const failures = observeFrontendFailures(page);

    await logIn(page);
    await expect(page.locator('.app-header')).toBeVisible();
    await expect(page.locator('.app-sidebar')).toBeVisible();
    await expect(page.locator('.app-main')).toBeVisible();
    await expect(page.locator('.sidebar-brand .brand-image')).toHaveCount(1);
    await expect(page.locator('.sidebar-brand .brand-text')).toHaveCount(0);
    await expect(page.locator('.app-content > .container-fluid > .card')).toHaveClass(/card-primary/);
    await expect(page.locator('.app-content > .container-fluid > .card')).toHaveClass(/card-outline/);
    await expect(page.locator('script[src*="jodit.min.js"]')).toHaveCount(0);
    await expect(page.locator('script[src*="admin-ui.js"]')).toHaveCount(1);
    if ((page.viewportSize()?.width || 0) <= 575) {
        const responsiveTable = page.locator('.app-content .table-responsive').first();
        await expect(responsiveTable).toHaveClass(/table-scrollable/);
        await expect(responsiveTable).toHaveAttribute('aria-label', 'Таблица с горизонтальной прокруткой');
    }

    const sidebarToggle = page.getByRole('button', {name: 'Открыть меню'});
    const bodyClassBefore = await page.locator('body').getAttribute('class');
    await sidebarToggle.click();
    await expect(page.locator('body')).toHaveClass(/sidebar-(?:collapse|open)/);
    expect(await page.locator('body').getAttribute('class')).not.toBe(bodyClassBefore);
    if ((page.viewportSize()?.width || 0) > 991) {
        await sidebarToggle.click();
        await expect(page.locator('body')).not.toHaveClass(/sidebar-collapse/);
    }

    const sidebarSearch = page.getByRole('searchbox', {name: 'Поиск по меню'});
    await expect(sidebarSearch).toBeVisible();
    await sidebarSearch.fill('Параметры');
    await expect(page.locator('#admin-sidebar-menu .nav-link', {hasText: 'Параметры'})).toBeVisible();
    await expect(page.locator('#admin-sidebar-menu > .nav-item', {hasText: 'Страницы'})).toBeHidden();
    await sidebarSearch.fill('несуществующий раздел');
    await expect(page.locator('[data-lte-search-empty]')).toBeVisible();
    await sidebarSearch.press('Escape');
    await expect(sidebarSearch).toHaveValue('');
    await expect(page.locator('#admin-sidebar-menu > .nav-item', {hasText: 'Страницы'})).toBeVisible();

    const treeParent = page.locator('.sidebar-menu > .nav-item:has(.nav-treeview)').first();
    if (await treeParent.count()) {
        await treeParent.locator(':scope > .nav-link').click();
        await expect(treeParent).toHaveClass(/menu-open/);
    }

    if ((page.viewportSize()?.width || 0) <= 991) {
        await page.mouse.click((page.viewportSize()?.width || 100) - 5, 100);
        await expect(page.locator('body')).not.toHaveClass(/sidebar-open/);
    }

    await page.locator('.user-menu > .dropdown-toggle').click();
    await expect(page.locator('.user-menu > .dropdown-menu')).toBeVisible();
    expect(failures).toEqual([]);
});

test('admin list pages keep the AdminLTE layout without frontend failures', async ({page}) => {
    test.skip(!process.env.SKIF_E2E_EMAIL || !process.env.SKIF_E2E_PASSWORD,
        'Set SKIF_E2E_EMAIL and SKIF_E2E_PASSWORD.');
    const failures = observeFrontendFailures(page);
    const paths = [
        '/admin/content/page',
        '/admin/content/news',
        '/admin/site_menu',
        '/admin/blocks',
        '/admin/poll',
        '/admin/form',
        '/admin/comments',
        '/admin/user',
        '/admin/content_type',
        '/admin/template',
        '/admin/redirect',
        '/admin/keyvalue',
    ];

    await logIn(page);
    for (const path of paths) {
        const response = await page.goto(path);
        expect(response?.status(), path).toBe(200);
        await expect(page.locator('.app-wrapper'), path).toBeVisible();
        await expect(page.locator('.app-content > .container-fluid > .card'), path).toHaveClass(/card-primary/);
        await expect(page.locator('.app-content > .container-fluid > .card'), path).toHaveClass(/card-outline/);
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth + 1), path)
            .toBe(true);
        expect(await page.locator('.nav-tabs > li:not(.nav-item), .nav-tabs > li > a:not(.nav-link)').count(), path)
            .toBe(0);
    }

    expect(failures).toEqual([]);
});

test('admin block navigation and CRUD filters work and remain separated', async ({page}) => {
    test.skip(!process.env.SKIF_E2E_EMAIL || !process.env.SKIF_E2E_PASSWORD,
        'Set SKIF_E2E_EMAIL and SKIF_E2E_PASSWORD.');
    const failures = observeFrontendFailures(page);

    await logIn(page);
    for (const path of ['/admin/content/news', '/admin/blocks']) {
        await page.goto(path);
        const filter = page.locator('.app-content .js-filter:has(.btn)').first();
        await expect(filter).toBeVisible();
        await expect(filter).toHaveCSS('display', 'inline-flex');
        await expect(filter).toHaveCSS('flex-wrap', 'wrap');
        await expect(filter).toHaveCSS('column-gap', '4px');
        await expect(filter).toHaveCSS('row-gap', '4px');
    }

    await page.goto('/admin/blocks');
    const templateTabs = page.locator('.app-content .nav-tabs > .nav-item > .nav-link');
    await expect(templateTabs.first()).toBeVisible();
    await expect(templateTabs.first()).toHaveCSS('display', 'block');
    await expect(page.locator('.app-content .nav-tabs > .nav-item > .nav-link.active')).toHaveCount(1);

    const titleCells = page.locator('.app-content .crud-table tbody tr td:nth-child(2)');
    await expect(titleCells.first()).toBeVisible();
    const title = (await titleCells.first().innerText()).trim();
    expect(title).not.toBe('');

    const titleFilter = page.locator('input[name="block_title"]');
    await Promise.all([
        page.waitForResponse((response) => (
            new URL(response.url()).pathname.startsWith('/admin/blocks')
            && response.request().method() === 'POST'
            && response.ok()
        )),
        titleFilter.fill(title),
    ]);
    await expect(titleCells.first()).toContainText(title);
    const filteredTitles = await titleCells.allTextContents();
    expect(filteredTitles.length).toBeGreaterThan(0);
    for (const filteredTitle of filteredTitles) {
        expect(filteredTitle).toContain(title);
    }

    expect(failures).toEqual([]);
});

test('content editor tabs use the Bootstrap 5 data API', async ({page}) => {
    test.skip(!process.env.SKIF_E2E_EMAIL || !process.env.SKIF_E2E_PASSWORD,
        'Set SKIF_E2E_EMAIL and SKIF_E2E_PASSWORD.');
    const failures = observeFrontendFailures(page);

    await logIn(page);
    let contentEditUrl = process.env.SKIF_E2E_CONTENT_EDIT_URL;
    if (!contentEditUrl) {
        await page.goto('/admin/content/page');
        const contentLinks = page.locator('a[href^="/admin/content/page/"]');
        await contentLinks.first().waitFor({state: 'visible'}).catch(() => {});
        const contentUrls = await contentLinks.evaluateAll((links) => links.map((link) => link.getAttribute('href')));
        contentEditUrl = contentUrls.find((url) => /^\/admin\/content\/page\/\d+$/.test(url || ''));
        test.skip(!contentEditUrl, 'No editable page fixture is available.');
    }

    await page.goto(contentEditUrl);
    await expect(page.locator('script[src*="jodit.min.js"]')).toHaveCount(1);
    await expect(page.locator('script[src*="glightbox.min.js"]')).toHaveCount(1);
    await page.getByRole('tab', {name: 'SEO'}).click();
    await expect(page.locator('#seo')).toHaveClass(/show/);
    await expect(page.locator('#seo input[name="description"]')).toBeVisible();
    expect(failures).toEqual([]);
});
