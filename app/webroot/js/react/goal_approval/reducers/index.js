import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import list from './list'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      list
    }, {
      routing: routerReducer
    })
  )
}
