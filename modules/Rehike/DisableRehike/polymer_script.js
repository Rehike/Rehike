(function(){
/**
 * Soft-disable client script: allows the user to easily return to Rehike using
 * the Polymer menu.
 * 
 * @author The Rehike Maintainers
 */

//////////////////////////////////////////////////////////////////////////////////////////////////
//
//  KNOWN BUGS:
//     - If you click on a different masthead menu too quick after opening the
//       account menu, then it will still add the Enable Rehike button. This is
//       such an edge case that I don't care to fix it, although it is probably
//       quite easy to do with a MutationObserver.
//

/**
 * @var {object}
 * @property {string[]} strings
 */
const DISABLE_POLYMER_CONFIG = PREPROCESSOR_DISABLE_POLYMER_CONFIG;

const REHIKE_LOGO_SVG = "m360 186 936 536-936 537V186Zm261 306c13 23 6 54-18 67-23 14-54 6-68-17-13-24-6-54 18-68 23-14 54-6 68 18Zm47 118c25 15 56 19 87 12l-23-38 32-19 176 298-32 19-127-216c-38 13-79 12-115 0l25 71 70 17 94 160-43 25-75-128-70-16 62 193-44 26-130-370c-8-23 3-44 19-53 16-10 35-11 52-2l42 21ZM519 816l51-18-58-163-27 10c-26 9-40 37-30 63l33 93c4 13 18 20 31 15Z";

/**
 * Waits for an element with the requested selector to exist.
 * 
 * Note that this expects the element to exist at some point, so it does not
 * ever time out.
 * 
 * @param {string} q CSS selector for the element.
 * @returns {Promise<Element>}
 */
async function waitForElm(q)
{
    return await waitForChildElm(document);
}

/**
 * Waits for an element with the selector to exist as a child of the parent.
 * 
 * @see waitForElm
 * 
 * @param {Element} parent 
 * @param {string} q CSS selector for the element
 * @returns {Promise<Element>}
 */
async function waitForChildElm(parent, q)
{
    while (parent.querySelector(q) == null)
    {
        await new Promise(r => requestAnimationFrame(r));
    }
    return parent.querySelector(q);
}

/**
 * Gets the ?enable_rehike URL for the user's current URL.
 * 
 * @returns {string}
 */
function getEnableRehikeUrlForCurrentUrl()
{
    let url = new URL(location.href);
    url.searchParams.set("enable_rehike", "1");

    return url.toString();
}

/**
 * Gets the Rehike logo as an SVG string.
 * 
 * @returns {string}
 */
function getRehikeLogoIcon()
{
    return (
        `<svg width="1543" height="1461" xmlns="http://www.w3.org/2000/svg" style="width: 100%;height: 100%;image-rendering: crisp-edges;fill:currentcolor;" viewBox="250 125 1183 1276">
            <path fill-rule="evenodd" clip-rule="evenodd" d="${REHIKE_LOGO_SVG}"></path>
        </svg>`);
}

/**
 * Creates the Enable Rehike button (and section) before the requested element.
 * 
 * @param {Element} beforeEl Element before which to insert ourself.
 * @returns {Promise<void>}
 */
async function createEnableRehikePolymerButton(beforeEl)
{
    let currentUrl = getEnableRehikeUrlForCurrentUrl();
    let rhSection = document.createElement("yt-multi-page-menu-section-renderer");
    rhSection.setAttribute("class", "style-scope ytd-multi-page-menu-renderer");
    rhSection.data = {
        items: [
            {
                compactLinkRenderer: {
                    isEnableRehikeButton: true,
                    icon: {
                        iconType: "UNKNOWN" // Will not create icon element
                    },
                    title: {
                        runs: [
                            {
                                text: DISABLE_POLYMER_CONFIG.strings.enableRehike || "Enable Rehike"
                            }
                        ]
                    },
                    navigationEndpoint: {
                        commandMetadata: {
                            webCommandMetadata: {
                                url: currentUrl,
                                webPageType: "WEB_PAGE_TYPE_UNKNOWN"
                            }
                        },
                        urlEndpoint: {
                            url: currentUrl
                        }
                    }
                }
            }
        ]
    };

    beforeEl.insertAdjacentElement("beforebegin", rhSection);

    // I love SVGs:
    let iconContainer = await waitForChildElm(rhSection, "yt-icon");
    let iconHtml = getRehikeLogoIcon();
    let icon = document.createElement("div");
    icon.innerHTML = iconHtml;
    icon = icon.firstChild;
    iconContainer.insertAdjacentElement("beforeend", icon);

    // Observe the icon to make sure that we remove ourself when needed.
    let iconObserver = new MutationObserver(function(records, self) {
        for (let i = icon; i.parentNode != null; i = i.parentNode)
        if ("YTD-COMPACT-LINK-RENDERER" == i.tagName)
        {
            if (!(i.data && i.data.isEnableRehikeButton))
            {
                icon.remove();
                self.disconnect();
            }
        }
    });

    iconObserver.observe(iconContainer, {
        subtree: true,
        childList: true,
        characterData: true
    });
}

/**
 * Handles the "yt-popup-opened" custom event.
 * 
 * @param {Event} e Popup opened event given to us by YouTube's JS.
 * @return {void}
 */
function onPopupOpened(e)
{
    let element = e.detail;
    let a, b;

    // ?. is too new a browser feature for my liking, sorry.
    if (
        "YTD-MULTI-PAGE-MENU-RENDERER" == element.tagName &&
        element.__shady &&
        element.__shady.parentNode &&
        element.__shady.parentNode.__data &&
        element.__shady.parentNode.__data.positionTarget &&
        element.__shady.parentNode.__data.positionTarget.data &&
        (a = element.__shady.parentNode.__data.positionTarget.data.menuRequest) &&
        a.commandMetadata &&
        (b = a.commandMetadata.webCommandMetadata) &&
        "/youtubei/v1/account/account_menu" == b.apiUrl
    )
    {
        let mutationHandler = async function(records, self) {
            let sections = element.querySelector("#sections");
            
            if (sections)
            {
                if (
                    element &&
                    element.data &&
                    element.data.sections &&
                    element.data.sections.length &&
                    sections.childElementCount == element.data.sections.length
                )
                {
                    createEnableRehikePolymerButton(sections.children[0]);
                    self.disconnect();
                }
            }
        };

        let mo = new MutationObserver(mutationHandler);

        mo.observe(element, {
            subtree: true,
            childList: true,
            characterData: true
        });
    }
}

/**
 * Insertion point.
 * 
 * @returns {void}
 */
function main()
{
    document.addEventListener("yt-popup-opened", onPopupOpened);
}

main();

})();