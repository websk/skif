# Frontend dependency inventory

This inventory describes the frontend state after the AdminLTE 4 and Bootstrap 5 migration. npm packages are copied into `public/assets/libraries` and committed because Composer consumers do not run npm during installation.

## npm-managed assets

| Package | Version | Runtime use | Loading policy |
| --- | --- | --- | --- |
| `admin-lte` | 4.10.0 | Administration and login layouts | Administration pages only. |
| `bootstrap` | 5.3.8 | All layouts and module templates | Core layout dependency. |
| `jquery` | 3.7.1 | Legacy project and CRUD widgets | Retained until the remaining jQuery widgets are replaced. |
| `jquery-ui` | 1.14.2 | Autocomplete and sortable widgets | Administration and legacy public widgets. |
| `jquery-validation` | 1.22.1 | Client-side form validation | Administration and public forms. |
| `glightbox` | 3.3.1 | Content image preview | Loaded by the admin layout only when the page contains `data-glightbox`. |
| `jodit` | 4.18.4 | Rich text editing and RichFileManager selection | Loaded only when rendered content contains a rich-text editor. |
| `ace-builds` | 1.45.0 | Block source editor | Loaded by the block editor template. |
| `@fortawesome/fontawesome-free` | 7.3.1 | Administration icons | Includes the v4 compatibility shim while legacy icon names remain. |
| `@hkvstore/rich-filemanager` | 3.2.0 | Authenticated file browsing and upload | Curated runtime files only. |
| `blueimp-file-upload` | 10.32.0 | CRUD upload widget | Retained for `websk/php-crud`. |
| `medium-editor` | 5.23.3 | CRUD rich text widget | Retained for `websk/php-crud`. |
| `tokenize2` | 1.3.5 | CRUD multiple autocomplete filter | Retained for `websk/php-crud`. |

## Removed packages

| Package | Reason |
| --- | --- |
| `ionicons` | No application or CRUD template references it; Font Awesome remains the icon provider. |
| `metismenu` | AdminLTE 4 provides the sidebar treeview. |
| `respond.js` | Bootstrap 5 supports modern evergreen browsers. |
| `eonasdan-bootstrap-datetimepicker`, `moment` | Replaced by native date and datetime controls. |
| `@fancyapps/fancybox` | Replaced by GLightbox. |
| `ckeditor4`, `flmngr` | Replaced by Jodit and the existing RichFileManager integration. |

## File manager backend

RichFileManager is restricted to authenticated administrators. Its prebuilt bundle contains legacy libraries that npm cannot audit independently, so replacing or modernizing that isolated bundle remains a separate security task.

## Build ownership and checks

The build owns `ace`, `admin-lte`, `blueimp-file-upload`, `bootstrap`, `crud`, `filemanager`, `font-awesome`, `glightbox`, `jodit`, `jquery`, `jquery-ui`, `jquery-validation`, `medium-editor`, and `tokenize2` under `public/assets/libraries`.

`npm run check` verifies JavaScript linting, forbidden legacy frontend contracts, generated tree reproducibility, local source-map references, CSS asset references, and asset size budgets. Production and complete dependency advisories are checked by `npm run audit:production` and `npm run audit:high`.

RichFileManager settings are sourced from `resources/frontend/filemanager`; do not edit the generated config directly.
