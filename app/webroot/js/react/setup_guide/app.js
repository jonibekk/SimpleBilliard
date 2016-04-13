import React from 'react'
import ReactDOM from 'react-dom'
import { createStore, combineReducers } from 'redux'
import { Provider } from 'react-redux'
import { Router, Route, IndexRoute, browserHistory } from 'react-router'
import { syncHistoryWithStore, routerReducer } from 'react-router-redux'

import * as reducers from './reducers'

// How do I write this simply?
import Index from './components/index'
import Top from './components/top'
import Goal from './components/goal'
import GoalImage from './components/goal_image'

const reducer = combineReducers({
  reducers,
  routing: routerReducer
})

const store = createStore(reducer)
const history = syncHistoryWithStore(browserHistory, store)

// Define setup-tuide routes
ReactDOM.render((
  <Provider store={store}>
    <Router history={history}>
      <Route path="/setup" component={Index} >
        <IndexRoute component={Top} />
        <Route path="goal" component={Goal} />
        <Route path="goal_image" component={GoalImage} />
          {/*<Route path="select" component={GoalSelect} >
            <Route path="detail" component={GoalSelectDetail} />
          </Route>*/}
      </Route>
    </Router>
  </Provider>
), document.getElementById("setup-guide"));
