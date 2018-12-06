import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import search from './search'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      search
    }, {
      routing: routerReducer
    })
  )
}
