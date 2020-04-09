import { readFileSync } from "fs";
import phpParser from "php-parser";

const ALLOWED = ["class", "interface", "function", "trait"];

function findKinds(obj: any, key: string, namespace = "") {
    let list: any[] = [];
    if (!obj) return list;
    if (obj instanceof Array) {
        for (const i in obj) {
            list = list.concat(findKinds(obj[i], key, namespace));
        }
        return list;
    }

    if (ALLOWED.indexOf(obj[key]) > -1 && obj.name) list.push({ ...obj.name, inNamespace: namespace });

    if (typeof obj == "object" && obj !== null) {
        const children = Object.keys(obj);
        if (children.length > 0) {
            // Correctly set namespace for next children
            const appendNamespace =
                obj.kind === "namespace" && typeof obj.name === "string"
                    ? `${obj.name.split("\\").filter(Boolean).join("\\")}\\`
                    : namespace;
            for (let i = 0; i < children.length; i++) {
                list = list.concat(findKinds(obj[children[i]], key, appendNamespace));
            }
        }
    }
    return list;
}

/**
 * Due to the fact that php-scoper does not support external global dependencies like WordPress
 * functions and classes we need to whitelist them. The best approach is to use the already
 * used stubs. Stubs are needed for PHP Intellisense so they need to be up2date, too.
 *
 * @see https://github.com/humbug/php-scoper/issues/303
 * @see https://github.com/humbug/php-scoper/issues/378
 */
function extractGlobalStubIdentifiers(files: string[]) {
    const result: string[] = [];

    const parser = new phpParser({
        parser: {
            extractDoc: true
        },
        ast: {
            withSource: false,
            withPositions: false
        }
    });

    for (const file of files) {
        const parsed = parser.parseCode(readFileSync(file, { encoding: "UTF-8" }));
        result.push(
            ...findKinds(parsed, "kind").map((id: { name: string; inNamespace: string }) => id.inNamespace + id.name)
        );
    }

    return result;
}

export { extractGlobalStubIdentifiers };
