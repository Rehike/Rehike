# Deferred APIs

**Deferred APIs**, also known as **deferred controllers**, are a programming pattern where a module manually, externally controls a promise.

Such controllers are usually, but necessarily, classes using the [`Deferred`](../Deferred.php) trait. Since this is just a programming pattern, no template from the framework is necessary (just helpful).

## Backed promises

The promises of deferred APIs are barebones. Unlike callback-instantiated promises, "backed" promises do not have an engine-managed, automatically-created event, and do not have a callback function defining their behaviour. They are, instead, managed from the outside.

To create a backed promise, all you need to do is call:

```php
new Promise()
```

without any arguments.

> [!NOTE]
> For the `Deferred` trait, call `$this->initPromise()` in the constructor instead.

> [!TIP]
> Callback-instantiated promises are really just self-backed promises. Upon their construction, a [`PromiseEvent`](../Promise/PromiseEvent.php) is created and inserted into the event loop (so long as the creation callback is not synchronous), and the arguments of the creation callback are just wrappers to the `resolve` and `reject` functions backed promises use.

### Events

Since backed promises don't have a backing event, your controller will also need to manage an event. [See the article on the event loop for details on how to create and manage events.](./event_loop.md)

### Resolution and rejection

When it's time to resolve or reject the promise, you want to call the `resolve` or `reject` methods on the promise object. This is similar to callback-instantiated promises, except that the names are strict and the clarity of the identity is enforced (whereas with function arguments, you could really type them as whatever and potentially trigger a `TypeError`)

## Rationale

(Isabella) To be honest, I am really not sure.