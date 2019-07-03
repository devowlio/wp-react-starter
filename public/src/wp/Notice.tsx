import React from "react";
import classNames from "classnames";

enum ENoticeType {
    Error = "Error",
    Info = "Info",
    Success = "Success"
}

interface IProps {
    type?: ENoticeType;
    children: React.ReactNode;
}

const Notice: React.FunctionComponent<IProps> = ({ type, children }) => {
    const classes = classNames({
        notice: true,
        "notice-error": type === ENoticeType.Error,
        "notice-info": type === ENoticeType.Info,
        "notice-success": type === ENoticeType.Success
    });

    return (
        <div className={classes}>
            <p>{children}</p>
        </div>
    );
};

export { Notice, ENoticeType };
