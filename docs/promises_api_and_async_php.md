# The Promises API & Asynchronous PHP

Rehike is a PHP application, which you would expect to be fairly synchronous, so it may be alarming when you see references to things like `Promise` and `async` all over the place. Well, fortunately, it's quite easy!

We implemented a singlethreaded asynchronous execution engine which functions similarly to that of general-purpose JavaScript engines, and implemented a clone of the ECMAScript Promise API alongside it.

Promises in Rehike typically wrap network APIs, as they give us a lot of flexibility over what happens in between network requests, while keeping a familiar and easy to read API. Here are the basics of Promises in Rehike:

## The fundamentals of asynchronous code

If you've never worked with asynchronous code before, then it can be a little tricky to wrap your mind around an abstraction like this. Instead, it may be easy to think of things through the callback model:

- You have four functions (to give clear names: `onGetVideoPage`, `requestVideoApi`, `videoRequestBackgroundWorker`, and `handleVideoApiResponse`). The background worker is called while the request is running, and can get other work done if necessary.
- `onGetVideoPage` is the insertion point. It is entered like any other function, and sets up the conditions for requesting the page's API. It may call `requestVideoApi` something like this:
    ```php
    requestVideoApi("videoRequestBackgroundWorker", "handleVideoApiResponse");
    ```
- `requestVideoApi` performs the network request and calls back `handleVideoApiResponse` once the network request finishes. It can do some other things as well, like perform additional background tasks while the request is running. For example:
    ```php
    function requestVideoApi(callable $backgroundCb, callable $responseCb): void
    {
        // Assume Network is some abstract native library, and we declared some abstract
        // example constants like VIDEO_API_URL and whatnot.
        $req = Network::open(VIDEO_API_URL);
        $req->send();
        
        do
        {
            if (!$req->isFinished())
            {
                $backgroundCb();
                usleep(100);
            }
            else
            {
                $responseCb($req->getResponseData());
                $req->cleanup();
            }
        }
        while ($req->isOpen());
    }
    ```
- `videoRequestBackgroundWorker` is called like a billion times while the request is running. It can perform some checks for other work that could be done asynchronously while the video is downloading, and do it ahead of time.
- `handleVideoApiResponse` is finally called when the the request is finished. It receives the response data, and can work with it from there.

Now, writing code like this gets pretty ugly pretty quickly, which is why abstractions like Promises were created in order to improve the readability of asynchronous code. However, abstractions can make a few things unclear. Hence, there are a couple axioms that you must remember:
- Asynchronous functions cannot be used synchronously. Considering things in a Promise context, if you have a function that returns a Promise that resolves with a network response, then you must queue a callback handler in order to get access to the network response. Otherwise, you will attempt to read the data before it even exists. It's called a Promise, because it only promises to get the data to you in the future.
- Also, `async` functions are fundamentally the same as Promises, just with a different syntax. You must still handle their contents by `yield`ing them within another `async` function, or by `then`ing them as a Promise.

## Basic Promise usage

So, enough introduction to asynchronous programming! How do we get to actually using them?

Promises are implemented in the CoffeeRequest library, but we have an alias class just for Rehike purposes. You can import Promises into a PHP file like such:

```php
use Rehike\Async\Promise;
```

We typically deal with InnerTube requests in Rehike, so here is an example of an InnerTube request, which returns a `Promise<Response>` (a Promise that wraps a CoffeeRequest network Response):

```php
$request = Network::innertubeRequest(
    action: "browse",
    body: [
        "browseId" => "FEwhat_to_watch"
    ]
);

$request->then(function(Response $response) {
    echo $response->getText();
})->catch(function(\Throwable $e) {
    echo "An error occurred.";
});
```

The above code sends out a request and, upon a response, echoes either the content of the response or an error message, depending on whether or not the network request was successful.

The Promise API function `then` queues a callback to be called when a Promise is resolved. That is, whenever the code that controls the Promise tells it to callback to the `then` callback registered for it.

Alternatively, the Promise API provides a function explicitly for error handling: `catch`. This is a callback function rather than a language construct, but it is still good for handling error conditions when a Promise is rejected.

If a Promise is neither resolved nor rejected, then it is considered pending, and additional code can be executed in the background.

### Async functions

Async functions are also emulated using Generators. These exist as syntactic sugar for Promises, which makes them significantly nicer to use for more complex workflows.

In order to use async functions, you must first import the wrapper function like such:

```php
use Rehike\Async\{
    function async,
    Promise
};
```

<sub>It is recommended that you import Promise alongside this.</sub>

Remember that async functions wrap Promises, so the return type of an async function is always a Promise. In the context of an async function, `yield` expressions "unwrap" the content of a `then` callback, and the `catch` language construct can be used for Promise exceptions too.

If you know async JavaScript or C#, then `yield` works exactly the same as `await` in this context.

Here is an example async function from some of our utility code:

```php
public static function getUcid($request): Promise/*<?string>*/
{
    return async(function() use (&$request) {
        if (in_array($request->path[0], ["channel", "user", "c"]))
        {
            switch($request->path[0])
            {
                case "channel":
                    $ucid = $request->path[1] ?? "";
                    if (substr($ucid, 0, 2) == "UC")
                    {
                        return $ucid;
                    }
                    else
                    {
                        return "";
                    }
                    break;
                case "user":
                case "c":
                    return yield self::getUcidFromUrl(explode("?", $_SERVER["REQUEST_URI"])[0]);
                    break;
            }
        }

        return yield self::getUcidFromUrl(explode("?", $_SERVER["REQUEST_URI"])[0]);
    });
}
```

Notice that you return `async`, which is a wrapper function with a callback that inserts you into this context.

Here is a translation of this same function using regular Promises. You'll see it gets a little annoying quick.

```php
public static function getUcid($request): Promise/*<?string>*/
{
    return new Promise(function ($resolve, $reject) {
        if (in_array($request->path[0], ["channel", "user", "c"]))
        {
            switch ($request->path[0])
            {
                case "channel":
                    $ucid = $request->path[1] ?? "";
                    if (substr($ucid, 0, 2) == "UC")
                    {
                        $resolve($ucid);
                        return;
                    }
                    else
                    {
                        $resolve("");
                        return;
                    }
                    break;
                case "user":
                case "c":
                    self::getUcidFromUrl(explode("?", $_SERVER["REQUEST_URI"])[0])
                        ->then(function(?string $response) use ($resolve) {
                            $resolve($response);
                        });
                    break;
            }
        }

        self::getUcidFromUrl(explode("?", $_SERVER["REQUEST_URI"])[0])
            ->then(function(?string $response) use ($resolve) {
                $resolve($response);
            });
    });
}
```

In most cases, doing things the `async` way is much more preferable.

### Creating a Promise

As described above, Promises are created through the object constructor `new Promise()`. The callback provided will be called instantly, and sets the conditions for resolving or rejecting itself. The rest of the work can be done asynchronously, but it is likely that most Promises being created through this manner will be synchronous, since they will likely not interface with any asynchronous native code such as cURL.

Along with this, we commonly use the following pattern for creating stub Promises. This is usually done conditionally, such that we only need to create a true Promise under certain conditions, i.e. for an API network request:

```php
new Promise(fn($r) => $r())
```

The Promise constructor callback receives a maximum of two arguments, both of which are callbacks themselves, named `$resolve` and `$reject`. Data passed into `$resolve` resolves the Promise with a said data, and data passed into `$reject` rejects the Promise with said exception. Once the event loop is ran, the `then` or `catch` callbacks will be called respectively for the action taken.

### Events

Rehike hides this away for the most part (i.e. in the base controller source code in `HitchhikerController`), but it can be useful to know how the Promises are actually gotten to. It is not guaranteed that a Promise's resolution will be handled immediately upon the condition being set, because Promises work within an event loop system.

The event loop is based around PHP's Generator system, and allows code registered in events to be called back in a loop. Note that the event loop isn't directly compatible with the standard runtime design, so events aren't checked for in between the execution of any ordinary functions.

The event loop can manually be invoked by importing `YukisCoffee\CoffeeRequest\Loop` and then calling `Loop::run()`. Once the event loop is started, it will only be closed once all events have finished running, or an event ends the event loop prematurely.

The standard creation of a Promise (provided a callback) internally creates an event whose resolution corresponds with the resolution or rejection of the wrapping Promise. However, a Promise created with no callback will have no automatically-created corresponding event, so this mechanism may be used to wrap an existing event. We consider this to be an advanced use, and it is used internally within the CoffeeRequest library.

If an event is created (i.e. by a Promise) while the event loop is already running, then it will simply be added to the list of running events, and it will be cycled through as usual. Therefore, running the event loop is only needed once every time it is necessary to switch from a fully-synchronous context to the asynchronous event-loop context.

## What's that little <> thing?!

Sometimes, we write the Promise names with a little comment following them that looks something like this: `Promise/*<Response>*/` or `Promise/*<string>*/` or whatever.

This is called a generic type (also known as a template type or type parameter), and it is a feature that exists in many other programming languages (but not PHP, unfortunately :P). The parent class can be defined with a placeholder type, which can then be substituted for other type names. This is particularly useful for wrappers, since it allows a great degree of code clarity and aids in static analysis of source code.

And yes, they nest! `Promise<Promise<Response>>` is very possible, but you probably don't want it nesting too deep...

Since they don't exist as a true PHP feature, we cannot rely on them for code security benefits, but we still use them in our code because it makes it easier to read and makes us pay closer attention to how things are working.