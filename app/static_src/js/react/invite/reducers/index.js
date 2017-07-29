import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import invite from './invite'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      invite
    }, {
      routing: routerReducer
    })
  )
}
