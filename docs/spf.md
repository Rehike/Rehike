# SPF (Structured Page Fragments)

[SPF, short for Structured Page Fragments, is YouTube's old library for implementing single-page applications.](//youtube.github.io/spfjs) In effect, this is just used to slightly optimize page downloads and make things a little nicer.

YouTube is what SPF was designed for, and it uses it in various areas, both for loading new pages (past the first time) and AJAX.

Rehike has historically used a custom library we made called SpfPhp for converting our generated HTML responses to SPF, but this was very slow, so we eventually ended up porting this to a Twig-based system like we generate HTML in the first place.

## Checking if a page is accessed via SPF in templates

You can simply check `yt.spf` for this purpose. For example:

```twig
{% if yt.spf %}
    {# SPF-specific logic #}
{% endif %}

{% if not yt.spf %}
    {# Things that shouldn't appear in SPF responses #}
{% endif %}
```

## The core of SPF in Rehike

SPF records, which are transported in JSON format, are pretty similar to HTML. In fact, it is most efficient to share template data between SPF responses and standard HTML responses. The two main things we want to share are inner HTML content and the class attribute.

You can find the shared data in the `page_fragments/` templates folder.

The root `core.twig` will route to `core/core_spf.twig` if `yt.spf` is set to true, which forms the SPF JSON response. On the other hand, `HitchhikerController` in PHP land will set the response type header to `application/json` so that the JS doesn't invalidate the response.