import React from "react";
import classNames from "classnames";

enum EButtonType {
    Primary,
    Secondary
}

interface IProps {
    className?: string;
    type: EButtonType;
    children: React.ReactNode;
    [key: string]: any;
}

const Button: React.FunctionComponent<IProps> = ({ className, type, children, ...rest }) => {
    const buttonClassName = classNames(className, {
        "button-primary": type === EButtonType.Primary,
        "button-secondary": type === EButtonType.Secondary
    });
    return (
        <button className={buttonClassName} {...rest}>
            {children}
        </button>
    );
};

export { Button, EButtonType };
