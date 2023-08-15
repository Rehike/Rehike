# Rehike Sign-In v2 Documentation

This documents changes made in this rewrite of the sign-in API Rehike uses and the motivations for rewriting it.

The original sign-in API was created early on in Rehike's life by Taniko. At the time, the team didn't focus on code quality as much as we do now, and the original implementation made some very weird choices. With Rehike v0.7 "Asynchike", these became very abundant and the sign-in system was lazily patched to adopt the new networking system.

## Problems with the original API

UX issues and prevalent bugs:
- The original API never implemented proper support for multiuser use. This is visible with Google Accounts that have multiple YouTube channels (Brand Accounts), but it is especially noticeable for users who use multiple Google Accounts at one time.
    - On the code side of things, we never bothered implementing support for the `X-Goog-Auth-User` header.
- The sign-in cache doesn't have proper error handling, and corruption of this file or a bug in Rehike can make Rehike fail to load for non-obvious reasons. The whole implementation of the sign-in cache was just kinda lazy.
- Also as an example of the above problem, the changing of a user's profile picture will not work correctly if Rehike is installed.
- Similarly, logging out of a session would not allow Rehike to load again until the session cookies were removed from the user's browser.
- Rehike does not account for Google Accounts which have no YouTube channels at all.

Code-exclusive problems:
- The code overall was very messy, and it only got worse with Asynchike. Type safety was not employed, and neither was proper exception handling.
- Almost all code was centralised in the original AuthManager class, save for parsing- and caching-related functionality.

## Focuses of the rewrite

The rewrite aims to:
- Fully support multiple Google accounts and YouTube channels.
- Elegantly manage caching and easily adapt to changes while remaining consistent.
- Have a clean, manageable, and asynchronous-by-design codebase design.

## Additional concerns

This update should be released as part of the "Asynchike" update to Rehike (version 0.7). Maintainers can still modify the legacy sign in system if they want to, especially while the main version continues to be 0.5, but I don't think that's going to happen.

## Additional plans

There are some aspects of this update that should influence further updates to improve overall code clarity.

As of this rewrite, Rehike does not have a shared caching system, and each module that uses caching needs its own implementation. We should move towards a single system in the future in order to better guarantee stability and consistency across all caching in Rehike.

The configuration system is similarly in need of a major reworking. This update introduces an experiments tab, which can be used to easily toggle between the old and new sign-in systems, but this has some limitation. As Rehike only works in English at the moment, the fact that configuration entries are only listed at all if they are localised is not too much of an issue, but it will become one in the future. The best implementation I see for Rehike configuration is a one source code file modification, plus additional modification for localising the settings, but otherwise it can fallback on English rather than not displaying the option at all.

## Code design

This section of the document will explain the design of the code of this update. Unlike the original design, which only had four files and was in desperate need of a cleanup, this update focuses on separating responsibilities and having classes that only encapsulate the functionality that they require. This creates a more manageable design that can easily be altered or improved.

The main, public API is implemented in the `SignIn` class. This class is to be used in order to evaluate, in PHP code, whether or not the used is signed in or to get additional information about the user session. This class is comparable to the former `Signin\API` class of the old system, which was created as a bit of a bandage in order to improve code clarity.

There are now additional sets of classes which divide responsibility more evenly. These are:
- **Info** classes, which are immutable classes used to retrieve information from the user's logged in session, such as their name and profile picture. These are publicly accessible and may be returned by the main `SignIn` API.
- **Builder** classes, which are used in order to create an Info class. They are only used internally, but mutable.
- **Parser** classes, which are used to scrape information from a third-party source, such as a YouTube API. These work in tandem with Builder classes in order to create the final Info class.
- **Manager** classes, of which there are presently only one, are used to implement an authentication service and coordinate the efforts to get information. It is likely that only the GAIA authentication manager (`GaiaAuthManager`) will be needed, unless Google or YouTube radically change their authentication systems within Rehike's lifetime.

New features are able to be easily added, due to the vastly improved code quality, including a proper error handling mechanism. The `SessionErrors` enum defines a bitmask which can be used to identify problems with signing in and handle them from anywhere within Rehike.

Although the new API hardly resembles the old one at all, there is some need for backwards compatibility with the `yt.signin` structure, which templates used heavily. During the writing of this update, many templates referenced `yt.signin.isLoggedIn` to verify if the user was logged in. This is currently still used by V2, but it may be abandoned in the future if the developers wish to further improve the clarity of responsibilities in templates (i.e. it would move to `rehike.signin`, being representative of Rehike state rather than a direct feature of the YouTube page) or support conditions such as the user being logged into an account without a channel.