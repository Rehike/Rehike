# Pages

The implementations of pages in Rehike are often divided across 4 different source code files, each in different directories, however they aren't as daunting as they may seem at first glance.

The reason for this division is because there are multiple different factors that are involved in their implementation. This is basically what is known as a CMV (controller-model-view) system. Here's how it basically works:

## Controllers

A controller is an insertion point. It is basically responsible for bootstrapping the page; getting the information needed and sending it to the user. That information needed is usually a HTML response that is generated from a template using data from a model.

Basically, this coordinates everything. In Rehike, controllers often have a flow like this:

- Network requests
- Create model (let's say, into `$pageModel`)
- `$yt->page = $pageModel;`

So it's not as difficult as it may seem.

## Models

Model really just means data, or object. In the Rehike codebase, we distinguish *modules* and *models* by how specialized their purpose is. Chances are, something that is used by only a page or a section of a page will be implemented by a model.

We usually restructure data, at least somewhat, from the direct InnerTube response with a standardized page model. This makes it easier to account for InnerTube API changes, should they occur, since the work is done in PHP rather than in the templates.

## Views/Templates

Views and templates are the same thing. In Rehike, we use Twig as a templating language.

The goal of a template, then, is quite simple: convert an object (data model) into HTML for consumption by the end user.

Templates are exposed the global `yt` variable. We prefer putting the page model in `yt.page`, however some old templates used in Rehike's source code may do this a different way. If you are implementing a new page into Rehike, please follow this new convention that we adopted.

## Routing

The last figure, of course, is getting to the controller in the first place. The file `router.php` in the source code root makes this easy, fortunately. This maps request method and page URL to a controller name, which is its file path minus the root ("`controllers/`") and the extension ("`.php`").