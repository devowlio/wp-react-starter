import { FC } from "react";
import { __ } from "../utils";

/* istanbul ignore next: Example implementations gets deleted the most time after plugin creation! */
const Widget: FC<{}> = () => (
    <div className="react-boilerplate-widget">
        <h3>{__("Hello, World!")}</h3>
        <p>{__("I got generated from your new plugin!")}</p>
    </div>
);

export { Widget };
