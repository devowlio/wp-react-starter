declare global {
    interface Window {
        /**
         * This number indicates the failed `GET` requests for all REST API calls.
         * See also `commonRequest.tsx`.
         */
        detectCorrupRestApiFailed: number;
    }
}

const WAIT_TO_TEST = 10000;

const NOTICE_ID = "notice-corrupt-rest-api";

/**
 * Register a new endpoint which needs to resolve to a valid JSON result. In this way we
 * can detect a corrupt REST API namespace e. g. it is blocked through a security plugin.
 */
function handleCorrupRestApi(resolve: Record<string, () => Promise<void>>, forceRerequest = false) {
    // Initially set
    window.detectCorrupRestApiFailed = window.detectCorrupRestApiFailed || 0;

    setTimeout(async () => {
        const notice = document.getElementById(NOTICE_ID);

        // Only in backend and when a corrupt REST API detected
        if (notice && (window.detectCorrupRestApiFailed > 0 || forceRerequest)) {
            for (const namespace of Object.keys(resolve)) {
                try {
                    await resolve[namespace]();
                } catch (e) {
                    notice.style.display = "block";
                    const li = document.createElement("li");
                    li.innerHTML = `- <code>${namespace}</code>`;
                    notice.childNodes[1].appendChild(li);
                }
            }
        }
    }, WAIT_TO_TEST);
}

export { handleCorrupRestApi };
