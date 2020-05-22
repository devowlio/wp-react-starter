/**
 * This is useful to loaders which need to be still "indexed" in the `rules` array.
 */
module.exports = function (content) {
    this.cacheable && this.cacheable(true);
    return content;
};
