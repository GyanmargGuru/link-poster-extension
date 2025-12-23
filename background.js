// Create context menu items when extension is installed
chrome.runtime.onInstalled.addListener(() => {
    // Context menu for links
    chrome.contextMenus.create({
        id: "postLink",
        title: "Post this link to Telegram",
        contexts: ["link"]
    });

    // Context menu for page (when not clicking on a link)
    chrome.contextMenus.create({
        id: "postPage",
        title: "Post page URL to Telegram",
        contexts: ["page"]
    });
});

// Handle context menu clicks
chrome.contextMenus.onClicked.addListener((info, tab) => {
    let urlToPost = "";

    if (info.menuItemId === "postLink") {
        urlToPost = info.linkUrl;
    } else if (info.menuItemId === "postPage") {
        urlToPost = info.pageUrl;
    }

    if (urlToPost) {
        // Store the URL and open popup
        chrome.storage.local.set({ urlToPost: urlToPost }, () => {
            // Get current window to center the popup
            chrome.windows.getCurrent((currentWindow) => {
                const popupWidth = 450;
                const popupHeight = 400;
                const left = Math.round(currentWindow.left + (currentWindow.width - popupWidth) / 2);
                const top = Math.round(currentWindow.top + (currentWindow.height - popupHeight) / 2);

                chrome.windows.create({
                    url: chrome.runtime.getURL("popup.html"),
                    type: "popup",
                    width: popupWidth,
                    height: popupHeight,
                    left: left,
                    top: top
                });
            });
        });
    }
});
