import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import circles from './circle'
import top from './top'
import profile from './profile'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      circles,
      top,
      profile
    },{
      routing: routerReducer
    })
  )
}
