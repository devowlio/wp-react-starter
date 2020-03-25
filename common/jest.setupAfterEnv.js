/* global afterEach jest */

// Make current executing package available as environment variable
process.env.PACKAGE_SCOPE = process.env.npm_package_name.split("/")[0];

// Clear all mocks after each test
afterEach(() => {
    jest.clearAllMocks();
});
