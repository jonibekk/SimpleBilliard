import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import circle from './circle'
import top from './top'
import profile from './profile'
import goal from './goal'
import action from './action'
import post from './post'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      circle,
      top,
      profile,
      goal,
      action,
      post
    },
    {
      routing: routerReducer
    })
  )
}
