# ⚠ ATTENTION ⚠

### REHIKE IS VERY EARLY IN DEVELOPMENT.
### THERE ARE VERY MANY MISSING FEATURES AND IT SHOULD NOT BE USED FOR ANY REASON OTHER THAN DEVELOPMENT.
### Please do not complain ELSEWHERE if ANYTHING doesn't work. Instead, [*make an issue here*](/issues) so we can actually track it.

<hr/>

# Rehike

<p align="center">
    <img src="branding/banner.png" alt="Rehike branding image">
</p>

A PHP project that aims to faithfully restore the old YouTube layout, known as Hitchhiker.

## Contributors

See [CONTRIBUTORS.md](CONTRIBUTORS.md).

## Installation

***Rehike is early in development. You should not expect a perfect user experience, should you choose to use it.***

At the moment, Rehike must be manually installed. In the future, a simplified installer application will be released to streamline installation. As it stands, you will require some knowledge to get through its installation.

Our main development operating system is Windows. As such, these instructions may differ if you are attempting to install Rehike on another operating system.

### Prerequisites
- PHP 7.4+
   - We recommend using [XAMPP 8+](https://www.apachefriends.org/index.html)
   - Linux users may need to manually install the [DOMDocument extension](https://www.php.net/manual/en/dom.setup.php).
- A proxy (to map it to `www.youtube.com`)
   - See [YukisCoffee/hitchhiker's instructions](https://github.com/YukisCoffee/hitchhiker#installation) on this.
   - A proxy is required for video playback, unless you have a CORS bypass extension installed on your browser.

## Acknowledgements

Rehike makes use of the following open source software:

- Composer
- Twig
- paquettg/php-html-parser
- [Return YouTube Dislike API](https://www.returnyoutubedislike.com/)

and much of their prerequisites.

## Player

YouTube has made some changes to the player which have had their experiment flags removed.
If you wish to revert the player back to the older design, use [this](https://github.com/YukisCoffee/yt-player-classicifier)!

## Thank you!

Whether or not you choose to contribute to Rehike, we appreciate you checking out our project!
