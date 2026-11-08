import {readFile, readdir} from 'node:fs/promises';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const roots = ['src', 'views', 'public/assets/scripts', 'public/assets/styles'];
const extensions = new Set(['.css', '.js', '.php']);
const forbidden = [
    ['Bootstrap 3 vertical navigation', /\bnav-stacked\b/],
    ['Bootstrap 3 data attribute', /\bdata-(?:toggle|target|dismiss|parent|ride|spy)=/],
    ['Bootstrap 3 form class', /\b(?:form-horizontal|control-label|input-group-addon|btn-default|btn-block)\b/],
    ['Bootstrap 3 responsive class', /\b(?:col-xs-|hidden-xs|hidden-sm|hidden-md|hidden-lg|img-responsive|sr-only)\b/],
    ['Bootstrap 3 component class', /\b(?:glyphicon|panel-(?:heading|body|footer)|progress-bar-(?:success|danger)|table-condensed)\b/],
    ['Bootstrap 3 alignment class', /\b(?:text-left|text-right|pull-left|pull-right)\b/],
    ['Bootstrap 3 tab state', /\btab-pane\s+in\b/],
    ['AdminLTE 2 contract', /\b(?:treeview-menu|sidebar-toggle|data-widget=)\b/],
    ['Bootstrap jQuery plugin API', /\$\([^\n;]+\)\.(?:modal|tab|collapse|dropdown|tooltip|popover)\s*\(/],
    ['Bootstrap 5 select styled as text input', /<select\b[^>]*class=["'][^"']*\bform-control\b/i],
];

async function filesUnder(relativeRoot) {
    const result = [];

    async function walk(directory) {
        for (const entry of await readdir(directory, {withFileTypes: true})) {
            const absolute = path.join(directory, entry.name);
            if (entry.isDirectory()) {
                await walk(absolute);
            } else if (entry.isFile() && extensions.has(path.extname(entry.name))) {
                result.push(absolute);
            }
        }
    }

    await walk(path.join(projectRoot, relativeRoot));
    return result;
}

const violations = [];
for (const root of roots) {
    for (const file of await filesUnder(root)) {
        const content = await readFile(file, 'utf8');
        for (const [label, pattern] of forbidden) {
            const match = pattern.exec(content);
            if (match) {
                const line = content.slice(0, match.index).split('\n').length;
                violations.push(`${path.relative(projectRoot, file)}:${line}: ${label}`);
            }
        }
    }
}

if (violations.length > 0) {
    throw new Error(`Legacy frontend contracts found:\n${violations.join('\n')}`);
}

console.log('Frontend contracts match Bootstrap 5 and AdminLTE 4.');
