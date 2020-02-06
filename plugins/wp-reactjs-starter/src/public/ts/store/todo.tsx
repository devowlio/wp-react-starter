import { observable, action } from "mobx";
import { TodoModel } from "../models";
import { RootStore } from "./stores";

/* istanbul ignore next: Example implementations gets deleted the most time after plugin creation! */
class TodoStore {
    public readonly rootStore: RootStore;

    @observable
    public todos: TodoModel[] = [];

    constructor(rootStore: RootStore) {
        this.rootStore = rootStore;
    }

    @action
    public add(title: TodoModel["title"]) {
        this.todos.push(new TodoModel(this, title));
    }
}

export { TodoStore };
