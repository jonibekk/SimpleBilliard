import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import circles from './circle'
import top from './top'
import profile from './profile'
import goal from './goal'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      circles,
      top,
      profile,
      goal
    },
    {
      routing: routerReducer
    })
  )
}
