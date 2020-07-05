import { RouteResponseInterface } from ".";

/**
 * Get the result of the `Response`. It also handles multiline responses, e. g.
 * a PHP `Notice:` message is output through a conflicting plugin:
 */
async function parseResult<TResponse extends RouteResponseInterface>(url: string, result: Response) {
    const cloneForFallback = result.clone();
    try {
        return (await result.json()) as TResponse;
    } catch (e) {
        // Something went wrong, try each line as result of a JSON string
        const body = await cloneForFallback.text();
        console.warn(`The response of ${url} contains unexpected JSON, try to resolve the JSON line by line...`, {
            body
        });
        let lastError: any;
        for (const line of body.split("\n")) {
            if (line.startsWith("[") || line.startsWith("{")) {
                try {
                    return JSON.parse(line) as TResponse;
                } catch (e) {
                    lastError = e;
                }
            }
        }
        throw lastError;
    }
}

export { parseResult };
