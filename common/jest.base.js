// Base jest configuration (https://jestjs.io/docs/en/configuration#coveragedirectory-string)

const path = require("path");

const pkg = require(path.resolve(process.env.PWD, "package.json"));
const rootName = pkg.name.match(/^@(.*)\//)[1];

module.exports = {
    setupFiles: ["<rootDir>/test/jest.setup.js"],
    testRegex: "(/test/jest/.*(\\.|/)(test|spec))\\.tsx$",
    transform: {
        "^.+\\.tsx?$": [
            "babel-jest",
            {
                babelrc: true,
                babelrcRoots: [
                    ".", // Keep the root as a root
                    // Also consider monorepo packages "root" and load their .babelrc files.
                    "../../packages/*",
                    "../../plugins/*"
                ]
            }
        ]
    },
    transformIgnorePatterns: ["node_modules/(?!@" + rootName + ")"],
    collectCoverage: false,
    coverageDirectory: "<rootDir>/coverage/jest",
    collectCoverageFrom: [
        "<rootDir>/lib/**/*.{tsx,ts}", // Packages
        "<rootDir>/src/public/ts/**/*.{tsx,ts}", // Plugins
        "!**/wp-api/*.{get,post,patch,put,delete,option,head}.{tsx,ts}"
    ],
    coverageThreshold: {
        global: {
            lines: 80
        }
    },
    snapshotSerializers: [],
    moduleNameMapper: {
        "\\.(css|less)$": "identity-obj-proxy"
    }
};
