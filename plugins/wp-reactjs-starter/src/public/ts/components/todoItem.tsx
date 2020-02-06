import classNames from "classnames";
import { useState } from "react";
import { observer } from "mobx-react";
import { TodoModel } from "../models";
import { __ } from "../utils";

/* istanbul ignore next: Example implementations gets deleted the most time after plugin creation! */
/**
 * Removable todo item.
 */
const TodoItem = observer(({ todo }: { todo: TodoModel }) => {
    const [isBold, setBold] = useState(false);
    return (
        <li onMouseEnter={() => setBold(true)} onMouseLeave={() => setBold(false)}>
            <label className={classNames(isBold && "attention")}>
                <input type="checkbox" />
                {todo.title}
            </label>
            &nbsp;
            <a href="#" onClick={() => todo.destroy()}>
                {__("Remove")}
            </a>
        </li>
    );
});

export { TodoItem };
