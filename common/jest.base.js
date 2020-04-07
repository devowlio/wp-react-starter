// Base jest configuration (https://jestjs.io/docs/en/configuration#coveragedirectory-string)

const path = require("path");

const pkg = require(path.resolve(process.env.PWD, "package.json"));
const isPlugin = !!pkg.slug;
const rootName = pkg.name.match(/^@(.*)\//)[1];

module.exports = {
    roots: ["<rootDir>/test/jest/"].concat(isPlugin ? ["<rootDir>/src/public/ts"] : ["<rootDir>/lib"]),
    testRegex: "(/test/jest/.*(\\.|/)(test|spec))\\.tsx$",
    setupFilesAfterEnv: ["<rootDir>/../../common/jest.setupAfterEnv.js"],
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
    reporters: [
        "default",
        [
            "jest-junit",
            {
                outputDirectory: "./test/junit",
                outputName: "jest.xml"
            }
        ]
    ],
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
        "\\.(css|less|scss)$": "identity-obj-proxy"
    }
};
