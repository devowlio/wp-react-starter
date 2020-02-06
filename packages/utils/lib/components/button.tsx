import React from "react";
import { FC, ReactNode } from "react";
import classNames from "classnames";

enum ButtonType {
    Primary,
    Secondary
}

interface ButtonProps {
    [key: string]: any;
    className?: string;
    type?: ButtonType;
    children: ReactNode;
}

const Button: FC<ButtonProps> = ({ className, type, children, ...rest }) => {
    const buttonClassName = classNames(className, {
        "button-primary": type === ButtonType.Primary,
        "button-secondary": type === ButtonType.Secondary || !type
    });
    return (
        <button className={buttonClassName} {...rest}>
            {children}
        </button>
    );
};

export { Button, ButtonType };
