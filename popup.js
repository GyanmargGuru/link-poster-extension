// DOM Elements
const urlField = document.getElementById('urlField');
const groupSelect = document.getElementById('groupSelect');
const postBtn = document.getElementById('postBtn');
const btnText = postBtn.querySelector('.btn-text');
const btnLoader = postBtn.querySelector('.btn-loader');
const result = document.getElementById('result');
const helpBtn = document.getElementById('helpBtn');

// Backend URL - configure this to your PHP server
const BACKEND_URL = 'http://localhost:8080/postlink.php';
// const BACKEND_URL = 'https://gyanmarg.guru/linkposter/postlink.php';

// Load URL from storage when popup opens
document.addEventListener('DOMContentLoaded', () => {
    chrome.storage.local.get(['urlToPost'], (data) => {
        if (data.urlToPost) {
            urlField.value = data.urlToPost;
            // Clear the stored URL
            chrome.storage.local.remove(['urlToPost']);
        } else {
            // If no URL in storage, try to get current tab URL
            chrome.tabs.query({ active: true, currentWindow: true }, (tabs) => {
                if (tabs[0] && tabs[0].url) {
                    urlField.value = tabs[0].url;
                } else {
                    urlField.value = 'No URL available';
                }
            });
        }
    });
});

// Handle group selection change - reset UI
groupSelect.addEventListener('change', () => {
    postBtn.classList.remove('hidden');
    hideResult();
});

// Handle Post button click
postBtn.addEventListener('click', async () => {
    const url = urlField.value;
    const groupIndex = groupSelect.value;

    if (!url || url === 'No URL available') {
        showResult('Please provide a valid URL', false);
        return;
    }

    if (groupIndex === '') {
        showResult('Please select a group', false);
        return;
    }

    // Show loading state
    setLoading(true);
    hideResult();

    try {
        const response = await fetch(BACKEND_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `url=${encodeURIComponent(url)}&group=${encodeURIComponent(groupIndex)}`
        });

        const data = await response.json();

        if (data.success) {
            showResult(data.message || 'Link posted successfully!', true);
            postBtn.classList.add('hidden'); // Hide button on success
        } else {
            showResult(data.message || 'Failed to post link', false);
        }
    } catch (error) {
        showResult('Error: Could not connect to server', false);
        console.error('Post error:', error);
    } finally {
        setLoading(false);
    }
});

// Handle Help button click
helpBtn.addEventListener('click', () => {
    chrome.tabs.create({ url: chrome.runtime.getURL('help.html') });
});

// Helper functions
function setLoading(loading) {
    postBtn.disabled = loading;
    btnText.classList.toggle('hidden', loading);
    btnLoader.classList.toggle('hidden', !loading);
}

function showResult(message, success) {
    result.textContent = message;
    result.className = `result ${success ? 'success' : 'error'}`;
}

function hideResult() {
    result.className = 'result hidden';
}
