import React from "react";
import { ReactNode, FC } from "react";
import classNames from "classnames";

enum NoticeType {
    Error = "Error",
    Info = "Info",
    Success = "Success"
}

interface NoticeProps {
    type?: NoticeType;
    children: ReactNode;
}

const Notice: FC<NoticeProps> = ({ type, children }) => {
    const classes = classNames({
        notice: true,
        "notice-error": type === NoticeType.Error,
        "notice-info": type === NoticeType.Info,
        "notice-success": type === NoticeType.Success
    });

    return (
        <div className={classes}>
            <p>{children}</p>
        </div>
    );
};

export { Notice, NoticeType };
