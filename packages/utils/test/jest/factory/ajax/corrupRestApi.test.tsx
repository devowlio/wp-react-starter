import { handleCorrupRestApi } from "../../../../lib/factory/ajax/corruptRestApi";

describe("handleCorrupRestApi", () => {
    jest.useFakeTimers();

    it("should not request anything when no notice is available", () => {
        (global as any).detectCorrupRestApiFailed = undefined;

        const mockResolver = jest.fn();
        handleCorrupRestApi({
            "wp/v1": mockResolver
        });

        expect((global as any).detectCorrupRestApiFailed).toBe(0);
        jest.runAllTimers();
        expect(mockResolver).not.toHaveBeenCalled();
    });

    it("should request when notice is available", () => {
        (global as any).detectCorrupRestApiFailed = 1;

        const noticeElement = {
            style: {
                display: "none"
            },
            childNodes: [
                null,
                {
                    appendChild: jest.fn()
                }
            ]
        };
        const liElement = {
            innerHTML: ""
        };
        const spyGetNotice = jest.spyOn((global as any).document, "getElementById").mockReturnValueOnce(noticeElement);
        const spyCreateLi = jest.spyOn((global as any).document, "createElement");

        const mockResolver = jest.fn();
        handleCorrupRestApi({
            "wp/v1": mockResolver
        });

        expect(mockResolver).not.toHaveBeenCalled();
        jest.runAllTimers();
        expect(spyGetNotice).toHaveBeenCalledWith("notice-corrupt-rest-api");
        expect(mockResolver).toHaveBeenCalled();
        expect(noticeElement.style.display).toBe("none");
        expect(noticeElement.childNodes[1].appendChild).not.toHaveBeenCalled();
        expect(spyCreateLi).not.toHaveBeenCalled();
        expect(liElement.innerHTML).toBe("");
    });

    it("should request when forced", () => {
        (global as any).detectCorrupRestApiFailed = 0;

        const noticeElement = {
            style: {
                display: "none"
            },
            childNodes: [
                null,
                {
                    appendChild: jest.fn()
                }
            ]
        };
        const liElement = {
            innerHTML: ""
        };
        const spyGetNotice = jest.spyOn((global as any).document, "getElementById").mockReturnValueOnce(noticeElement);
        const spyCreateLi = jest.spyOn((global as any).document, "createElement");

        const mockResolver = jest.fn();
        handleCorrupRestApi(
            {
                "wp/v1": mockResolver
            },
            true
        );

        expect(mockResolver).not.toHaveBeenCalled();
        jest.runAllTimers();
        expect(spyGetNotice).toHaveBeenCalledWith("notice-corrupt-rest-api");
        expect(mockResolver).toHaveBeenCalled();
        expect(noticeElement.style.display).toBe("none");
        expect(noticeElement.childNodes[1].appendChild).not.toHaveBeenCalled();
        expect(spyCreateLi).not.toHaveBeenCalled();
        expect(liElement.innerHTML).toBe("");
    });

    it("should show notice when an error occured", () => {
        (global as any).detectCorrupRestApiFailed = 1;

        const noticeElement = {
            style: {
                display: "none"
            },
            childNodes: [
                null,
                {
                    appendChild: jest.fn()
                }
            ]
        };
        const liElement = {
            innerHTML: ""
        };
        jest.spyOn((global as any).document, "getElementById").mockReturnValueOnce(noticeElement);
        const spyCreateLi = jest.spyOn((global as any).document, "createElement").mockReturnValueOnce(liElement);

        const mockResolver = jest.fn().mockImplementation(() => {
            throw "Some error occured during request";
        });
        handleCorrupRestApi({
            "wp/v1": mockResolver
        });

        jest.runAllTimers();
        expect(noticeElement.style.display).toBe("block");
        expect(noticeElement.childNodes[1].appendChild).toHaveBeenCalledWith(expect.any(Object));
        expect(spyCreateLi).toHaveBeenCalledWith("li");
    });
});
