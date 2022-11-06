# Frequently asked questions

## **Why can't I put newlines in my comments?**

This is a oddity with Hitchhiker JS. You need to use two newlines, much like Reddit markdown.

## **Why can't I reply to a specific user?**

Ditto, Hitchhiker itself was incapable of this. Eventually, Rehike will rewrite certain behaviours in order to fix these problems.

## **Why is the player so big? Can it be reverted?**

YouTube has made some changes to the player which have had their experiment flags removed.
If you wish to revert the player back to a smaller design, [check out this userstyle by the project's authors!](https://github.com/YukisCoffee/yt-player-classicifier)

Currently, support for older player revisions is not in consideration. It is very much possible, however to support it would require reworking our entire player architecture.

## **What's that `.version` file?**

For version tracking, we've implemented this file. This allows us to track version information without the user having Git installed on their computer or cloning from GitHub.

## **Can't you do Creator Studio?**

Good question. Technically? We probably *could* do it. But we're not going to. Here's a few reasons.

- Actions are protected through attestation using Google's botguard, which is the same technology that powers the backend of reCAPTCHA. This security mechanism makes it extremely difficult to forge a request to the Creator Studio API, which unfortunately, we need to be able to do in order to implement a custom frontend.
- There already exists [an official Creator Studio frontend](//studio.youtube.com) that is separate from Rehike. So use is not impaired by having Rehike installed.
- Rehike's userbase will remain small and Creator Studio is used much less than other parts of YouTube. As such, it will be harder to ensure stability, and limited reward for implementing it.