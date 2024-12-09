document.addEventListener('DOMContentLoaded', () => {
    const postForm = document.querySelector('#post-form');
    const pageForm = document.querySelector('#page-form');

    if (!postForm && !pageForm) return;

    postForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        await triggerNextjsApi(postId());
    });

    pageForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        await triggerNextjsApi(pageId());
    });
});

async function postId() {
    return document.querySelector('#title').value ? '' : document.querySelector('#post_ID').value;
}

async function pageId() {
    return document.querySelector('#title').value ? '' : document.querySelector('#page_ID').value;
}

async function triggerNextjsApi(id) {
    try {
        const response = await fetch('http://localhost:3000/api?secret=rrCQF66sWqLfyleeZKkBrpIcm8fp1S98LWOMYvmc5A29J23UmtsHGIfLMCHtQit5&type=post&id=' + id);
        console.log('api')

        if (!response.ok) {
            throw new Error('Failed to invalidate cache');
        }

        console.log('Cache invalidated successfully');
    } catch (error) {
        console.error('Error invalidating cache:', error);
    }
}
