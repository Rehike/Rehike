# Client/Server IPC in Nepeta

Nepeta provides a client/server interprocess communication system. This allows Nepeta modules to be implemented regardless of language (technically), and allows for the sandboxing of Nepeta modules in a different process to prevent errors in them from affecting the rest of the Rehike environment.

## Server

The Nepeta IPC server is always running in the main PHP context, within which the rest of Rehike's code also executes.

## Client

The Nepeta IPC client is language-agnostic. It runs in a separate process from the rest of Rehike's code and uses inter-process communication and marshalling for data exchange.