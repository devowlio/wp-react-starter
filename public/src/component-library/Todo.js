import { Component } from 'react';
import { inject, observer } from 'mobx-react';
import Button from 'wp/button';

@observer
class TodoItem extends Component {
    render() {
        const { todo } = this.props;
        return <li>
            <label>
                <input type="checkbox" />
                { todo.text } 
            </label>&nbsp;
            <a href="#" onClick={ () => todo.remove() }>Remove</a>
        </li>;
    }
}

/**
 * Quoted from MobX home page:
 * 
 * The observer function / decorator can be used to turn ReactJS components into reactive components.
 * It wraps the component's render function in mobx.autorun to make sure that any data that is used
 * during the rendering of a component forces a re-rendering upon change. It is available through the
 * separate mobx-react package.
 * 
 * @see https://mobx.js.org/refguide/observer-component.html
 * 
 * The mobx-react package also provides the Provider component that can be used to pass down stores using
 * React's context mechanism. To connect to those stores, pass a list of store names to inject, which will
 * make the stores available as props.
 * N.B. the syntax to inject stores has changed since mobx-react 4, always use inject(stores)(component) or @inject(stores) class Component.... Passing store names directly to observer is deprecated.
 * 
 * @see https://mobx.js.org/refguide/observer-component.html#connect-components-to-provided-stores-using-inject
 */
@inject('store') // Inject the store from the <Provider>
@observer
class Todo extends Component {
    constructor(props) {
        super(props);
        this.state = {
            inputText: ''
        };
    }
    
    handleAdd = () => {
        this.props.store.addTodo(this.state.inputText);
        this.setState({ inputText: '' });
    }
    
    handleChange = e => {
        this.setState( { inputText: e.target.value } );
    }
    
    render() {
        const { store: { todos } } = this.props,
            { inputText } = this.state;
        console.log(todos.completedCount);
        return <div className="wp-styleguide--buttons">
			<h2>Todo list ({ todos.length })</h2>
			<p>This section demonstrates a mobx-state-tree Todo list (no peristence to server).</p>
			<input value={ inputText } onChange={ this.handleChange } type="text" className="regular-text" placeholder="What needs to be done?" />
			<Button type="primary" disabled={ !inputText.length } onClick={ this.handleAdd }>Add</Button>
			<ul>
			    { todos.map(t => <TodoItem key={ t.id } todo={ t } />) }
			    { !todos.length && <li>No entries</li> }
			</ul>
		</div>;
    }
}

export default Todo;