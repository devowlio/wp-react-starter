import renderer from "react-test-renderer";
import React from "react";
import { Notice, NoticeType } from "../../../lib";
import { Provider } from "../helpers";

describe("notice", () => {
    it("should render notice with default arguments", () => {
        const tree = renderer
            .create(
                <Provider>
                    <Notice>Test</Notice>
                </Provider>
            )
            .toJSON();
        expect(tree).toMatchSnapshot();
    });

    it("should render notice with error type", () => {
        const tree = renderer
            .create(
                <Provider>
                    <Notice type={NoticeType.Error}>Test</Notice>
                </Provider>
            )
            .toJSON();
        expect(tree).toMatchSnapshot();
    });

    it("should render notice with info type", () => {
        const tree = renderer
            .create(
                <Provider>
                    <Notice type={NoticeType.Info}>Test</Notice>
                </Provider>
            )
            .toJSON();
        expect(tree).toMatchSnapshot();
    });

    it("should render notice with success type", () => {
        const tree = renderer
            .create(
                <Provider>
                    <Notice type={NoticeType.Success}>Test</Notice>
                </Provider>
            )
            .toJSON();
        expect(tree).toMatchSnapshot();
    });
});
