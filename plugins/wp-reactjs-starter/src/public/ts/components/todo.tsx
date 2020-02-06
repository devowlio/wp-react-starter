import { FC, useState } from "react";
import { observer } from "mobx-react";
import { Button, ButtonType } from "@wp-reactjs-multi-starter/utils";
import { TodoItem } from "./todoItem";
import { useStores } from "../store";
import { __ } from "../utils";

/* istanbul ignore next: Example implementations gets deleted the most time after plugin creation! */
/**
 * The mobx-react package also provides the Provider component that can be used to pass down stores using
 * React's context mechanism. For this you have to use the custom useStores hook implemented in ../stores
 * in your function components.
 *
 * @see https://mobx-react.js.org/recipes-context
 * @see https://mobx.js.org/refguide/observer-component.html#connect-components-to-provided-stores-using-inject
 */
const TodoOverview: FC<{}> = observer(() => {
    const [inputText, setInputText] = useState("");
    const { todoStore } = useStores();

    return (
        <div className="wp-styleguide--buttons">
            <h2>
                {__("Todo list")} ({todoStore.todos.length})
            </h2>
            <p>{__("This section demonstrates a MobX Todo list (no peristence to server).")}</p>
            <input
                value={inputText}
                onChange={(event) => setInputText(event.target.value)}
                type="text"
                className="regular-text"
                placeholder={__("What needs to be done?")}
            />
            <Button type={ButtonType.Primary} disabled={!inputText.length} onClick={() => todoStore.add(inputText)}>
                {__("Add")}
            </Button>
            <ul>
                {todoStore.todos.map((t) => (
                    <TodoItem key={t.id} todo={t} />
                ))}
                {!todoStore.todos.length && <li>No entries</li>}
            </ul>
        </div>
    );
});

export { TodoOverview };
