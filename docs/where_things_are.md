# Where things are

The Rehike codebase is nicely organized, at least in terms of files, so you shouldn't have too much trouble finding your way around.

Still, the codebase organization may feel uncomfortable to a newcomer. Here is the structure, demystified.

Auxiliary things (these are not a part of the Rehike source code, per se, but are used for other purposes):

- `.git-hooks/` - Development Git scripts. If you are going to contribute back to Rehike, please copy these into `.git/hooks`.
- `.github/` - Stores GitHub-related things such as issue templates and README images.
- `cache/` - Created on first execution; stores generated cache files for the user session.
- `config.json` - Created on first execution; stores the user's current configuration.

Source code files:

- `controllers/` - Insertion points (controllers) for each of Rehike's unique pages.
- `i18n/` - Translation files.
- `includes/` - Low-level PHP source code, or anything that isn't in a class.
- `models/` - Code used for page data models; InnerTube model converters or entirely custom data models. These are what's fed into the template engine.
- `modules/` - General Rehike source code, including the base and most operations.
- `modules/generated/` - Generated source files, i.e. from the `src/protos` folder. You shouldn't modify this unless you know what you're doing.
- `src/` - Non-PHP source files. This includes things like JS, CSS, protobuf, and images.
- `static/` - Static content files, such as images or JavaScript files.
- `template/` - Twig source code files for HTML/JS templates.
- `vendor/` - Composer-downloaded modules. You shouldn't modify this unless you know what you're doing.
- `.htaccess` - Apache server configuration file. This simply contains a directive to send all traffic through index.php.
- `index.php` - PHP insertion point. You shouldn't modify this unless you know what you're doing.
- `integrity_check.php` - Performs certain precondition checks to verify that the current runtime environment is compatible with Rehike.
- `router.php` - Maps URL patterns to a page controller; ultimately responsible for loading the controller insertion points.
