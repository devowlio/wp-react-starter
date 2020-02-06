import { BaseOptions } from "../../lib";

describe("BaseOptions", () => {
    describe("slugCamelCase", () => {
        it("should convert slug to camel case with hyphen", () => {
            const slug = "my-plugin";
            const should = "myPlugin";

            const actual = BaseOptions.slugCamelCase(slug);

            expect(actual).toBe(should);
        });

        it("should convert slug to camel case without hyphen", () => {
            const slug = "myplugin";
            const should = "myplugin";

            const actual = BaseOptions.slugCamelCase(slug);

            expect(actual).toBe(should);
        });
    });

    describe("getPureSlug", () => {
        const env = { slug: "my-plugin" };

        it("should return slug from current process", () => {
            const should = String(env.slug);

            const actual = BaseOptions.getPureSlug(env);

            expect(actual).toBe(should);
        });

        it("should return slug from current process with camel case", () => {
            const should = "myPlugin";

            const actual = BaseOptions.getPureSlug(env, true);

            expect(actual).toBe(should);
        });
    });
});
