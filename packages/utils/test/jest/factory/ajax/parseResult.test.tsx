import { parseResult } from "../../../../lib/factory/ajax/parseResult";

jest.mock("deepmerge");
jest.mock("url-parse");
jest.mock("whatwg-fetch");

describe("parseResult", () => {
    const spyWarn = jest.spyOn(console, "warn").mockImplementation(() => {
        // Silence is golden.
    });

    it("should parse result with correct JSON return", async () => {
        const should = { test: 1 };
        const mockJson = jest.fn().mockReturnValue(Promise.resolve(should));
        const mockClone = jest.fn();
        const result = {
            clone: mockClone,
            json: mockJson
        } as any;
        const url = "https://";

        const actual = await parseResult(url, result);

        expect(actual).toBe(should);
        expect(mockJson).toHaveBeenCalled();
        expect(mockClone).toHaveBeenCalled();
    });

    it("should parse result with PHP notice in it", async () => {
        const should = { test: 1 };
        const mockJson = jest.fn().mockImplementation(() => {
            throw "";
        });
        const mockText = jest.fn().mockReturnValue(`Notice:\n${JSON.stringify(should)}`);
        const mockClone = jest.fn().mockReturnValue({
            text: mockText
        });
        const result = {
            clone: mockClone,
            json: mockJson
        } as any;
        const url = "https://";

        const actual = await parseResult(url, result);

        expect(actual).toEqual(should);
        expect(spyWarn).toHaveBeenCalled();
    });

    it("should parse array result with PHP notice in it", async () => {
        const should = [{ test: 1 }];
        const mockJson = jest.fn().mockImplementation(() => {
            throw "";
        });
        const mockText = jest.fn().mockReturnValue(`Notice:\n${JSON.stringify(should)}`);
        const mockClone = jest.fn().mockReturnValue({
            text: mockText
        });
        const result = {
            clone: mockClone,
            json: mockJson
        } as any;
        const url = "https://";

        const actual = await parseResult(url, result);

        expect(actual).toEqual(should);
    });

    it("should throw when no JSON string found", async () => {
        const mockJson = jest.fn().mockImplementation(() => {
            throw "";
        });
        const mockText = jest.fn().mockReturnValue(`Notice:\nAnother Notice:\n`);
        const mockClone = jest.fn().mockReturnValue({
            text: mockText
        });
        const result = {
            clone: mockClone,
            json: mockJson
        } as any;
        const url = "https://";

        await expect(parseResult(url, result)).rejects.toBeUndefined();
    });

    it("should throw when an invalid JSON string found", async () => {
        const mockJson = jest.fn().mockImplementation(() => {
            throw "";
        });
        const mockText = jest.fn().mockReturnValue(`Notice:\nAnother Notice:\n{t}`);
        const mockClone = jest.fn().mockReturnValue({
            text: mockText
        });
        const result = {
            clone: mockClone,
            json: mockJson
        } as any;
        const url = "https://";

        await expect(parseResult(url, result)).rejects.toBeDefined();
    });
});
