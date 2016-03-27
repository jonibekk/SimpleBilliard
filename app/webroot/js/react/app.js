import React from 'react';
import ReactDOM from 'react-dom';
import Counter from './components/Counter';

import counter from './reducers/index';
import { createStore } from 'redux';

const store = createStore(counter);

function render() {
  ReactDOM.render(
    <Counter
      value={store.getState()}
      onIncrement={() => store.dispatch({ type: 'INCREMENT' })}
      onDecrement={() => store.dispatch({ type: 'DECREMENT' })}
      />,
    document.getElementById('setup-guide')
  );
}

render();
store.subscribe(render);
