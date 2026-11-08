import {createHash} from 'node:crypto';
import {
    access,
    cp,
    mkdir,
    mkdtemp,
    readFile,
    readdir,
    rm,
    stat,
    writeFile,
} from 'node:fs/promises';
import {tmpdir} from 'node:os';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const nodeModulesRoot = path.join(projectRoot, 'node_modules');
const projectAssetsRoot = path.join(projectRoot, 'resources', 'frontend');
const composerAssetsRoot = path.join(projectRoot, 'vendor', 'websk', 'php-crud');
const assetsRoot = path.join(projectRoot, 'public/assets/libraries');

const managedDirectories = [
    'ace',
    'admin-lte',
    'blueimp-file-upload',
    'bootstrap',
    'crud',
    'font-awesome',
    'filemanager',
    'glightbox',
    'jodit',
    'jquery',
    'jquery-ui',
    'jquery-validation',
    'medium-editor',
    'tokenize2',
];

const obsoleteDirectories = [
    'bootstrap-datetimepicker',
    'ckeditor',
    'fancybox',
    'metisMenu',
    'moment',
    'respond.js',
    'ionicons',
];

const copies = [
    ['blueimp-file-upload/css/jquery.fileupload.css', 'blueimp-file-upload/css/jquery.fileupload.css'],
    ['blueimp-file-upload/js/jquery.fileupload.js', 'blueimp-file-upload/js/jquery.fileupload.js'],
    ['blueimp-file-upload/js/jquery.fileupload-process.js', 'blueimp-file-upload/js/jquery.fileupload-process.js'],
    ['blueimp-file-upload/js/jquery.fileupload-validate.js', 'blueimp-file-upload/js/jquery.fileupload-validate.js'],
    ['bootstrap/dist/js/bootstrap.bundle.min.js', 'bootstrap/js/bootstrap.bundle.min.js'],
    ['bootstrap/dist/js/bootstrap.bundle.min.js', 'bootstrap/js/bootstrap.min.js'],
    ['bootstrap/dist/js/bootstrap.bundle.min.js.map', 'bootstrap/js/bootstrap.bundle.min.js.map'],
    ['bootstrap/dist/css/bootstrap.min.css', 'bootstrap/css/bootstrap.min.css'],
    ['bootstrap/dist/css/bootstrap.min.css.map', 'bootstrap/css/bootstrap.min.css.map'],
    ['jquery/dist/jquery.min.js', 'jquery/jquery.min.js'],
    ['jquery-ui/dist/jquery-ui.min.js', 'jquery-ui/jquery-ui.min.js'],
    ['jquery-ui/dist/themes/base/jquery-ui.min.css', 'jquery-ui/themes/base/jquery-ui.min.css'],
    ['jquery-ui/dist/themes/base/images', 'jquery-ui/themes/base/images'],
    ['jquery-validation/dist/jquery.validate.js', 'jquery-validation/jquery.validate.js'],
    ['medium-editor/dist/js/medium-editor.min.js', 'medium-editor/js/medium-editor.min.js'],
    ['medium-editor/dist/css/medium-editor.min.css', 'medium-editor/css/medium-editor.min.css'],
    ['medium-editor/dist/css/themes/default.min.css', 'medium-editor/css/themes/default.min.css'],
    ['tokenize2/dist/tokenize2.min.js', 'tokenize2/tokenize2.min.js'],
    ['tokenize2/dist/tokenize2.min.css', 'tokenize2/tokenize2.min.css'],
    ['tokenize2/LICENSE', 'tokenize2/LICENSE'],
    ['glightbox/dist/js/glightbox.min.js', 'glightbox/glightbox.min.js'],
    ['glightbox/dist/css/glightbox.min.css', 'glightbox/glightbox.min.css'],
    ['@fortawesome/fontawesome-free/css/all.min.css', 'font-awesome/css/all.min.css'],
    ['@fortawesome/fontawesome-free/css/v4-shims.min.css', 'font-awesome/css/v4-shims.min.css'],
    ['@fortawesome/fontawesome-free/webfonts', 'font-awesome/webfonts'],
    ['@hkvstore/rich-filemanager/index.html', 'filemanager/index.html'],
    ['@hkvstore/rich-filemanager/images', 'filemanager/images'],
    ['@hkvstore/rich-filemanager/languages/en.json', 'filemanager/languages/en.json'],
    ['@hkvstore/rich-filemanager/languages/ru.json', 'filemanager/languages/ru.json'],
    ['@hkvstore/rich-filemanager/src/css/libs-main.css', 'filemanager/src/css/libs-main.css'],
    ['@hkvstore/rich-filemanager/src/css/filemanager.min.css', 'filemanager/src/css/filemanager.min.css'],
    ['@hkvstore/rich-filemanager/libs/jquery.contextmenu/dist/font', 'filemanager/src/css/font'],
    ['@hkvstore/rich-filemanager/libs/custom-scrollbar-plugin/mCSB_buttons.png', 'filemanager/src/css/mCSB_buttons.png'],
    ['@hkvstore/rich-filemanager/src/js/libs-main.js', 'filemanager/src/js/libs-main.js'],
    ['@hkvstore/rich-filemanager/src/js/filemanager.min.js', 'filemanager/src/js/filemanager.min.js'],
    ['@hkvstore/rich-filemanager/src/js/libs-fileupload.js', 'filemanager/src/js/libs-fileupload.js'],
    ['@hkvstore/rich-filemanager/src/templates', 'filemanager/src/templates'],
    ['@hkvstore/rich-filemanager/themes/default', 'filemanager/themes/default'],
    ['@hkvstore/rich-filemanager/libs/lazyload/dist/lazyload.min.js', 'filemanager/libs/lazyload/dist/lazyload.min.js'],
    ['@hkvstore/rich-filemanager/libs/cldrjs/cldr-core/supplemental/likelySubtags.json', 'filemanager/libs/cldrjs/cldr-core/supplemental/likelySubtags.json'],
    ['@hkvstore/rich-filemanager/libs/cldrjs/cldr-core/supplemental/timeData.json', 'filemanager/libs/cldrjs/cldr-core/supplemental/timeData.json'],
    ['@hkvstore/rich-filemanager/libs/cldrjs/cldr-core/supplemental/weekData.json', 'filemanager/libs/cldrjs/cldr-core/supplemental/weekData.json'],
    ['@hkvstore/rich-filemanager/libs/cldrjs/cldr-dates/en/ca-gregorian.json', 'filemanager/libs/cldrjs/cldr-dates/en/ca-gregorian.json'],
    ['@hkvstore/rich-filemanager/libs/cldrjs/cldr-dates/ru/ca-gregorian.json', 'filemanager/libs/cldrjs/cldr-dates/ru/ca-gregorian.json'],
    ['@hkvstore/rich-filemanager/libs/cldrjs/cldr-numbers/en/numbers.json', 'filemanager/libs/cldrjs/cldr-numbers/en/numbers.json'],
    ['@hkvstore/rich-filemanager/libs/cldrjs/cldr-numbers/ru/numbers.json', 'filemanager/libs/cldrjs/cldr-numbers/ru/numbers.json'],
    ['@hkvstore/rich-filemanager/libs/jQuery-File-Upload/css/dropzone.css', 'filemanager/libs/jQuery-File-Upload/css/dropzone.css'],
    ['@hkvstore/rich-filemanager/libs/jQuery-File-Upload/img', 'filemanager/libs/jQuery-File-Upload/img'],
    ['@hkvstore/rich-filemanager/libs/ViewerJS', 'filemanager/libs/ViewerJS'],
    ['ace-builds/src-min/ace.js', 'ace/ace.js'],
    ['ace-builds/src-min/mode-html.js', 'ace/mode-html.js'],
    ['ace-builds/src-min/worker-html.js', 'ace/worker-html.js'],
    ['ace-builds/src-min/mode-sql.js', 'ace/mode-sql.js'],
    ['ace-builds/src-min/mode-php.js', 'ace/mode-php.js'],
    ['ace-builds/src-min/mode-xml.js', 'ace/mode-xml.js'],
    ['ace-builds/src-min/worker-php.js', 'ace/worker-php.js'],
    ['ace-builds/src-min/worker-xml.js', 'ace/worker-xml.js'],
    ['ace-builds/src-min/mode-text.js', 'ace/mode-text.js'],
    ['ace-builds/src-min/mode-javascript.js', 'ace/mode-javascript.js'],
    ['ace-builds/src-min/worker-javascript.js', 'ace/worker-javascript.js'],
    ['ace-builds/src-min/mode-json.js', 'ace/mode-json.js'],
    ['ace-builds/src-min/worker-json.js', 'ace/worker-json.js'],
    ['ace-builds/src-min/ext-searchbox.js', 'ace/ext-searchbox.js'],
    ['ace-builds/src-min/theme-crimson_editor.js', 'ace/theme-crimson_editor.js'],
    ['jodit/es2021/jodit.min.js', 'jodit/jodit.min.js'],
    ['jodit/es2021/jodit.min.css', 'jodit/jodit.min.css'],
    ['admin-lte/dist/js/adminlte.min.js', 'admin-lte/js/adminlte.min.js'],
    ['admin-lte/dist/js/adminlte.min.js.map', 'admin-lte/js/adminlte.min.js.map'],
    ['admin-lte/dist/css/adminlte.min.css', 'admin-lte/css/AdminLTE.min.css'],
    ['admin-lte/dist/css/adminlte.min.css.map', 'admin-lte/css/adminlte.min.css.map'],
];

const projectCopies = [
    ['filemanager/filemanager.config.json', 'filemanager/config/filemanager.config.json'],
    ['filemanager/filemanager.config.json', 'filemanager/config/filemanager.config.default.json'],
    ['filemanager/filemanager.init.js', 'filemanager/config/filemanager.init.js'],
];

const composerCopies = [
    ['resources/css/crud.css', 'crud/crud.css'],
];

async function assertSourcesExist() {
    for (const [source] of copies) {
        try {
            await access(path.join(nodeModulesRoot, source));
        } catch {
            throw new Error(`Missing source asset: node_modules/${source}. Run npm ci first.`);
        }
    }

    for (const [source] of projectCopies) {
        try {
            await access(path.join(projectAssetsRoot, source));
        } catch {
            throw new Error(`Missing project asset: resources/frontend/${source}.`);
        }
    }

    for (const [source] of composerCopies) {
        try {
            await access(path.join(composerAssetsRoot, source));
        } catch {
            throw new Error(`Missing Composer asset: vendor/websk/php-crud/${source}. Run composer install first.`);
        }
    }
}

async function build(targetRoot) {
    await mkdir(targetRoot, {recursive: true});

    for (const directory of [...managedDirectories, ...obsoleteDirectories]) {
        await rm(path.join(targetRoot, directory), {recursive: true, force: true});
    }

    for (const [source, destination] of copies) {
        const sourcePath = path.join(nodeModulesRoot, source);
        const destinationPath = path.join(targetRoot, destination);
        const sourceStat = await stat(sourcePath);

        await mkdir(path.dirname(destinationPath), {recursive: true});
        await cp(sourcePath, destinationPath, {
            recursive: sourceStat.isDirectory(),
            force: true,
        });
    }

    for (const [source, destination] of projectCopies) {
        const sourcePath = path.join(projectAssetsRoot, source);
        const destinationPath = path.join(targetRoot, destination);

        await mkdir(path.dirname(destinationPath), {recursive: true});
        await cp(sourcePath, destinationPath, {force: true});
    }

    for (const [source, destination] of composerCopies) {
        const sourcePath = path.join(composerAssetsRoot, source);
        const destinationPath = path.join(targetRoot, destination);

        await mkdir(path.dirname(destinationPath), {recursive: true});
        await cp(sourcePath, destinationPath, {force: true});
    }

    await normalizeTextFiles(path.join(targetRoot, 'filemanager'));
}

async function normalizeTextFiles(root) {
    const textExtensions = new Set(['.css', '.html', '.js', '.json']);

    for (const file of await listFiles(root)) {
        if (!textExtensions.has(path.extname(file).toLowerCase())) {
            continue;
        }

        const filePath = path.join(root, file);
        const content = await readFile(filePath, 'utf8');
        let normalized = content.replace(/^\uFEFF/, '').replace(/\r\n/g, '\n');
        if (file.endsWith('libs/jQuery-File-Upload/css/dropzone.css')) {
            normalized = normalized.replace('background-image: url(../../../images/wait30trans.gif);', 'background-image: none;');
        }

        if (content !== normalized) {
            await writeFile(filePath, normalized, 'utf8');
        }
    }
}

async function listFiles(root) {
    const files = [];

    async function walk(directory) {
        for (const entry of await readdir(directory, {withFileTypes: true})) {
            const absolutePath = path.join(directory, entry.name);
            if (entry.isDirectory()) {
                await walk(absolutePath);
            } else if (entry.isFile()) {
                files.push(path.relative(root, absolutePath));
            }
        }
    }

    await walk(root);
    return files.sort();
}

async function digest(filePath) {
    return createHash('sha256').update(await readFile(filePath)).digest('hex');
}

async function compareTrees(expectedRoot, actualRoot) {
    const expectedFiles = await listFiles(expectedRoot);
    const actualFiles = await listFiles(actualRoot);

    if (expectedFiles.join('\n') !== actualFiles.join('\n')) {
        const expected = new Set(expectedFiles);
        const actual = new Set(actualFiles);
        const missing = expectedFiles.filter((file) => !actual.has(file));
        const unexpected = actualFiles.filter((file) => !expected.has(file));
        throw new Error(`Asset file list differs. Missing: ${missing.join(', ') || 'none'}. Unexpected: ${unexpected.join(', ') || 'none'}.`);
    }

    for (const file of expectedFiles) {
        if (await digest(path.join(expectedRoot, file)) !== await digest(path.join(actualRoot, file))) {
            throw new Error(`Generated asset differs: ${file}`);
        }
    }

    return expectedFiles.length;
}

async function assertNoServerFiles(root) {
    const forbiddenExtensions = new Set(['.cgi', '.phar', '.php', '.phtml']);
    const files = await listFiles(root);
    const forbidden = files.filter((file) => forbiddenExtensions.has(path.extname(file).toLowerCase()));

    if (forbidden.length > 0) {
        throw new Error(`Server-side files found in generated assets: ${forbidden.join(', ')}`);
    }

    return files.length;
}

async function assertManagedAssetsSafe(root) {
    let count = 0;
    for (const directory of managedDirectories) {
        count += await assertNoServerFiles(path.join(root, directory));
    }
    return count;
}

async function assertObsoleteAssetsAbsent(root) {
    for (const directory of obsoleteDirectories) {
        try {
            await access(path.join(root, directory));
            throw new Error(`Obsolete asset directory still exists: ${directory}`);
        } catch (error) {
            if (!error || error.code !== 'ENOENT') {
                throw error;
            }
        }
    }
}

async function assertLocalReferencesExist(root) {
    const missing = [];

    for (const file of await listFiles(root)) {
        if (!file.endsWith('.css') && !file.endsWith('.js')) {
            continue;
        }

        const filePath = path.join(root, file);
        const content = await readFile(filePath, 'utf8');
        const references = [...content.matchAll(/sourceMappingURL=([^\s*]+)/g)].map((match) => match[1]);

        if (file.endsWith('.css')) {
            for (const match of content.matchAll(/url\(\s*(['"]?)([^'"\)]+)\1\s*\)/g)) {
                references.push(match[2]);
            }
        }

        for (const reference of references) {
            const normalized = reference.split(/[?#]/, 1)[0].trim();
            if (!normalized
                || normalized.startsWith('/')
                || normalized.startsWith('#')
                || normalized.startsWith('data:')
                || normalized.startsWith('http:')
                || normalized.startsWith('https:')
                || normalized.startsWith('var(')) {
                continue;
            }

            try {
                await access(path.resolve(path.dirname(filePath), decodeURIComponent(normalized)));
            } catch {
                missing.push(`${file} -> ${reference}`);
            }
        }
    }

    if (missing.length > 0) {
        throw new Error(`Missing local asset references: ${missing.slice(0, 20).join(', ')}`);
    }
}

async function assertAssetBudget(root) {
    const maximumBytes = 16 * 1024 * 1024;
    let totalBytes = 0;

    for (const file of await listFiles(root)) {
        totalBytes += (await stat(path.join(root, file))).size;
    }

    if (totalBytes > maximumBytes) {
        throw new Error(`Managed assets exceed the 16 MiB budget: ${totalBytes} bytes.`);
    }

    return totalBytes;
}

await assertSourcesExist();

if (process.argv.includes('--check')) {
    const temporaryRoot = await mkdtemp(path.join(tmpdir(), 'skif-assets-'));
    try {
        await build(temporaryRoot);
        let count = 0;
        for (const directory of managedDirectories) {
            count += await compareTrees(
                path.join(temporaryRoot, directory),
                path.join(assetsRoot, directory),
            );
        }
        await assertManagedAssetsSafe(temporaryRoot);
        await assertNoServerFiles(path.join(projectRoot, 'public', 'assets'));
        await assertObsoleteAssetsAbsent(assetsRoot);
        await assertLocalReferencesExist(temporaryRoot);
        const totalBytes = await assertAssetBudget(temporaryRoot);
        console.log(`Assets are complete, references resolve, and the tree is reproducible: ${count} files, ${totalBytes} bytes.`);
    } finally {
        await rm(temporaryRoot, {recursive: true, force: true});
    }
} else {
    await build(assetsRoot);
    const count = await assertManagedAssetsSafe(assetsRoot);
    await assertNoServerFiles(path.join(projectRoot, 'public', 'assets'));
    await assertObsoleteAssetsAbsent(assetsRoot);
    await assertLocalReferencesExist(assetsRoot);
    const totalBytes = await assertAssetBudget(assetsRoot);
    console.log(`Built ${count} managed asset files, ${totalBytes} bytes.`);
}
