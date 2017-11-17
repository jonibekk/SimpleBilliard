import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import campaign from './campaign'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      campaign
    }, {
      routing: routerReducer
    })
  )
}
