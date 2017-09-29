import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import payment from './payment'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      payment
    }, {
      routing: routerReducer
    })
  )
}
