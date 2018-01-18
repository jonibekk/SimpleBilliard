import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import saved_item from './saved_item'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      saved_item
    }, {
      routing: routerReducer
    })
  )
}
