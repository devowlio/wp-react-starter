// Unfortunately the jest config can not be placed directly to package.json
// because then it does not support inheritance.

const base = require("../../../common/jest.base");

/**
 * As we are not using a "real" node package jest can not resolve
 * the source code automatically.
 */
base.collectCoverageFrom.unshift("src/public/ts/**/*.{tsx,ts}");

module.exports = base;
