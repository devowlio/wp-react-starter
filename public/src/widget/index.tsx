import React from "react";
import { __ } from "../util/i18n";
import "./style.scss";

const Widget: React.FunctionComponent<{}> = () => (
    <div className="react-boilerplate-widget">
        <h3>{__("Hello, World!")}</h3>
        <p>{__("I got generated from your new plugin!")}</p>
    </div>
);

export { Widget };
