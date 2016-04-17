import { createStore, combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import * as reducers from '../reducers'

const reducer = combineReducers({
  reducers,
  routing: routerReducer
})

export default function configureStore() {
    const store = createStore(reducer);
    return store;
}
