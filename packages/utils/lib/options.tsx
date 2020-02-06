/**
 * See PHP file inc/Assets.php.
 */
abstract class BaseOptions {
    public slug: string;
    public textDomain: string;
    public version: string;
    public restUrl?: string;
    public restNamespace?: string;
    public restRoot?: string;
    public restQuery?: {};
    public restNonce?: string;
    public publicUrl?: string;

    /**
     * Convert a slug like "my-plugin" to "myPlugin". This can
     * be useful for library naming (window[""] is bad because the hyphens).
     *
     * @param slug
     * @returns
     */
    public static slugCamelCase(slug: string) {
        return slug.replace(/-([a-z])/g, (g) => g[1].toUpperCase());
    }

    /**
     * Get the slug from the current process (webpack) instead of the PHP plugin output.
     * For some cases you need to use that.
     *
     * @param env
     * @param camelCase
     */
    public static getPureSlug(env: typeof process.env, camelCase = false) {
        return camelCase ? BaseOptions.slugCamelCase(env.slug) : env.slug;
    }
}

export { BaseOptions };
