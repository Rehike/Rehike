# Rehike

<p align="center">
    <img src="branding/banner.png" alt="Rehike branding image">
</p>

A PHP project that aims to faithfully restore the old YouTube layout, known as Hitchhiker.

## Contributors

See [CONTRIBUTORS.md](CONTRIBUTORS.md);

## Installation

Rehike requires PHP 7.4+. **Installation is completely manual at the moment.** In the future, a packaged installer is planned to be released to aid with installation and setup.

Developer-tested configuration uses [XAMPP](https://www.apachefriends.org/download.html) as a web server. All three 

The easiest way to redirect `www.youtube.com` to Rehike (without causing any conflictions) is to use a proxy program. All three of these are tested by the developers and known to work, however, Fiddler is the most user-friendly solution and thus it is recommended to be used.
- [Fiddler Classic (**recommended**)](https://www.telerik.com/download/fiddler/fiddler4)
- [Charles (download trial)](https://www.charlesproxy.com/download/)
- [mitmproxy](https://mitmproxy.org/)

### Fiddler Classic Configuration

<sub>Don't use Fiddler Everywhere. It sucks.</sub>

Enable decryption of HTTPS traffic (from browsers only), then set up an AutoResponder rule that redirects `REGEX:https://www.youtube.com/(.*)` to `http://127.0.0.1/$1` and tick the box that allows unmatched request passthrough. If you then want to hide Fiddler from view, then press `CTRL`+`M`. Fiddler will be accessible in the system tray.

I recommend using Fiddler Classic if you're on Windows, because it uses less RAM than Charles and it's more user-friendly than mitmproxy.

### Charles Configuration

Enable SSL proxying and install the root certificate in your browser, then go to "Map Remote" and add a rule to redirect host `www.youtube.com` to `http://127.0.0.1`. If you then want to hide Charles from view, press `CTRL`+`,` to open "Preferences", then click "Minimise to system tray".

### mitmproxy Configuration

You really got yourself into a mess, huh?
```py
import mitmproxy.http
        
class HitchhikerFrontendRedirect:
    def __init__(self):
        print("Hitchhiker Frontend Redirect active")
    
    def request(self, flow: mitmproxy.http.HTTPFlow):
        if "www.youtube.com" in flow.request.pretty_host:
            flow.request.host = "127.0.0.1"
            flow.request.port = 80
            flow.request.scheme = "http"
    
addons = [ HitchhikerFrontendRedirect() ]
```

But wait! There's more. mitmproxy doesn't allow this on HTTP 2 hosts, meaning you need to pass the `--no-http2` argument. Additionally, it likes to break WebSocket traffic, which was a common complaint levied against it in the GoodTube Discord server when that was around (it broke Discord image uploads), so you need to pass a TCP passthrough regex that ignores WS traffic. As such, I concocted this launch command just for you mitmproxy users:

```sh
mitmdump -s YOUR_PYTHON_FILE.py --no-http2 --tcp !(www.youtube.com)
```

Note that on Windows, the command is `mitmdump`. On Unix systems, this will be `mitmproxy` instead. Otherwise, the launch command should be the same.

I tested this with Discord image uploading, and it seems to work as it should. Please make an issue if problems arise.

### Hostsfile Configuration

Too badass for mitmproxy? Give hostsfile a try!

I did all the work for you, actually. The requests library overcomes the need for a proxy at all by using manual nameserver lookups that bypass your system's hostsfile for requests. **This may also be useful for you if you are having trouble with any proxy.**

All you need to do is host this on localhost, add the rule to your hostsfile, enable it in the frontend `config.json` file, and then... [set up SSL for it to work at all](https://www.webdesignvista.com/install-ssl-certificate-for-localhost-xampp-windows/). As it turns out, YouTube uses HSTS, so you will probably get an SSL error that your browser doesn't let you bypass instead of the YouTube website.

<sub>I still recommend using a proxy, just because it's more user friendly 🥰</sub>
