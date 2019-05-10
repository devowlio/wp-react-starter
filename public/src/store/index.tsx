import { types, getRoot, destroy, Instance } from "mobx-state-tree";

/**
 * This is a simple demonstation of a To-Do store with To-Do-Items.
 *
 * @see https://github.com/mobxjs/mobx-state-tree/tree/master/packages/mst-example-todomvc
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

const TodoStore = types
    .model({
        todos: types.array(Todo)
    })
    .actions((self) => ({
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
