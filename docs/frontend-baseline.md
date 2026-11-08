# Frontend behavior baseline

This baseline records the user-visible behavior that must remain available while the frontend dependencies are modernized. The application is served from `public/`, uses versioned `/assets/<version>/...` URLs, and runs the administration interface under `/admin`.

## Smoke scenarios

| Area | Route or entry point | Expected behavior |
| --- | --- | --- |
| Public application | `/` | Returns an HTML page without a PHP or JavaScript error. |
| Administration | `/admin` | Shows the administration page for an authenticated user or the login flow for a guest. |
| Login | `/auth/login_form` | Shows the login form with Bootstrap/AdminLTE styling and client-side validation. |
| Navigation | Administration layout | The sidebar opens nested menu items, highlights the current section, and remains usable at narrow viewport widths. |
| CRUD lists | Content, blocks, forms, polls, redirects, menus | Tables, pagination, filters, edit links, and action buttons remain usable. |
| CRUD forms | Module edit pages | Labels, controls, validation messages, save actions, and redirects remain functional. |
| Date fields | Content edit form | `created_at`, `published_at`, and `unpublished_at` accept and display the configured date and time format. |
| Rich text | Content editors | Jodit initializes in an isolated iframe, preserves existing HTML, synchronizes the textarea, and opens RichFileManager for images and files. |
| File manager | `/filemanager` | An authenticated administrator can browse, upload, rename, delete, and select files. |
| Content photos | Content photo editor | A valid image uploads, progress and errors are visible, the list refreshes, and an image can be removed or selected as default. |
| Gallery | Content photo views | Clicking a thumbnail opens the full image and gallery navigation. |
| CAPTCHA | `/captcha/generate` | Returns a valid PNG image. |

## JavaScript integration points

- `views/layouts/layout.adminLTE.tpl.php`: jQuery, jQuery UI, Bootstrap 5, AdminLTE 4, validation, GLightbox, and Jodit.
- `views/layouts/layout.admin_login.tpl.php`: jQuery, Bootstrap, AdminLTE, and Font Awesome.
- `views/layouts/layout.main.tpl.php`: jQuery, jQuery UI, Bootstrap, validation, and GLightbox.
- `src/WebSK/Skif/Content/views/admin_content_form_edit.tpl.php`: native `datetime-local` and `date` controls, plus GLightbox.
- `src/WebSK/Skif/Content/views/content_photo_form_edit.tpl.php`: project-owned image upload module and GLightbox with dynamic gallery reloads.
- `src/WebSK/Skif/Blocks/RequestHandlers/Admin/views/block_edit.tpl.php`: Ace with the Crimson Editor theme and PHP mode.
- `src/WebSK/Skif/RichTextEditor/views/rich_text_editor.tpl.php`: Jodit configuration and RichFileManager integration.
- `public/assets/scripts/rich-text-editor.js`: Jodit initialization, same-origin file selection, and textarea synchronization.
- `resources/frontend/filemanager/`: project-owned RichFileManager 3.2 configuration for the authenticated `/filemanager` connector and Jodit `postMessage` selection.

## Baseline evidence

Browser verification covers the public page and guest administration page at desktop and mobile viewport sizes. Runtime verification also covers `/captcha/generate` as a PNG response. The isolated Docker smoke workflow uses a temporary database and administrator to verify authentication, content CRUD, image upload, RichFileManager access, logout, and the PHP `E_ALL` deprecation gate without modifying the developer database.
