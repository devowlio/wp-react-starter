import { untrailingslashit, trailingslashit } from "../../lib";

describe("helpers", () => {
    describe("untrailingslashit", () => {
        const should = "/home/foo/bar";
        it("should remove last slash", () => {
            const path = "/home/foo/bar/";

            const actual = untrailingslashit(path);

            expect(actual).toBe(should);
        });

        it("should remove double slash", () => {
            const path = "/home/foo/bar//";

            const actual = untrailingslashit(path);

            expect(actual).toBe(should);
        });

        it("should do nothing", () => {
            const path = "/home/foo/bar";

            const actual = untrailingslashit(path);

            expect(actual).toBe(should);
        });
    });

    describe("trailingslashit", () => {
        const should = "/home/foo/bar/";

        it("should add slash", () => {
            const path = "/home/foo/bar";

            const actual = trailingslashit(path);

            expect(actual).toBe(should);
        });

        it("should do nothing", () => {
            const path = "/home/foo/bar/";

            const actual = trailingslashit(path);

            expect(actual).toBe(should);
        });
    });
});
