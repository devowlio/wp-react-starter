import renderer from "react-test-renderer";
import React from "react";
import { Button, ButtonType } from "../../../lib";
import { Provider } from "../helpers";

describe("button", () => {
    it("should render button with default arguments", () => {
        const tree = renderer
            .create(
                <Provider>
                    <Button>Test</Button>
                </Provider>
            )
            .toJSON();
        expect(tree).toMatchSnapshot();
    });

    it("should render button with primary type", () => {
        const tree = renderer
            .create(
                <Provider>
                    <Button type={ButtonType.Primary}>Test</Button>
                </Provider>
            )
            .toJSON();
        expect(tree).toMatchSnapshot();
    });

    it("should render button with title attribute", () => {
        const tree = renderer
            .create(
                <Provider>
                    <Button title="Test">Test</Button>
                </Provider>
            )
            .toJSON();
        expect(tree).toMatchSnapshot();
    });
});
