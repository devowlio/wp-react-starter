import { types, getRoot, destroy, Instance } from "mobx-state-tree";

/**
 * A single list item within the [[TodoStore]].
 */
const Todo = types
    .model({
        text: types.string,
        id: types.identifierNumber
    })
    .actions((self) => ({
        remove() {
            getRoot<ITodoStore>(self).removeTodo(self as ITodo);
        }
    }));

export type ITodo = Instance<typeof Todo>;

/**
 * A todo store which allows you to add and remove [[Todo]] items.
 *
 * @see https://github.com/mobxjs/mobx-state-tree/tree/master/packages/mst-example-todomvc
 */
const TodoStore = types
    .model({
        todos: types.array(Todo)
    })
    .actions((self) => ({
        /**
         * Add a [[Todo]] item by passed text.
         *
         * @param text
         */
        addTodo(text: string) {
            const id = self.todos.reduce((maxId, todo) => Math.max(todo.id, maxId), -1) + 1;
            self.todos.unshift({
                id,
                text
            });
        },

        removeTodo(todo: ITodo) {
            destroy(todo);
        }
    }));

export type ITodoStore = Instance<typeof TodoStore>;

export { TodoStore, Todo };
