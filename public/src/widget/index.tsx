import React from "react";
import { translate } from "../util/i18n";
import "./style.scss";

const Widget: React.FunctionComponent<{}> = () => (
    <div className="react-boilerplate-widget">
        <h3>{translate("widgetTitle")}</h3>
        <p>{translate("widgetDescription")}</p>
    </div>
);

export { Widget };
