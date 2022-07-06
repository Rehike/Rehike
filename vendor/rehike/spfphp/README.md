# SpfPhp

A PHP library that parses and transforms HTML into the JSON required by YouTube's [SPF.js](//github.com/youtube/spfjs).

## Installation

The recommended installation method is via [Composer](//getcomposer.org):

```sh
composer require rehike/spfphp
```

After installation, use the library like such:

```php
require "vendor/autoload.php"; // Include Composer packages

use SpfPhp\SpfPhp;
```

## Get started

SpfPhp is designed with the most portability in mind. With that, it does not require the use of or integrate with any particular templating engine.

To use this with any templating engine, or just PHP alone, simply wrap all output with these following functions:

```php
<?php
SpfPhp::beginCapture();

?>
<html>
    <head>
        <title>Example</title>
    </head>
    <body>
        <div id="content" x-spfphp-capture>Hello world!</div>
    </body>
</html>
<?php

// Then tell SpfPhp we need it
SpfPhp::autoRender();
```

### [Click here for further documentation](//github.com/Rehike/SpfPhp/wiki)

### [See an example project](//github.com/YukisCoffee/spfphp-example)

# Acknowledgements

SpfPhp makes use of the following open-source software:

- [voku/simple_html_dom](//github.com/voku/simple_html_dom)
   - [YukisCoffee's fork](//github.com/yukiscoffee/simple_html_dom) is used instead, in order to patch a bug with the upstream library.
