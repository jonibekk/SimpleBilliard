import { combineReducers } from 'redux'
import { CREATE_CIRCLE, SELECT_CIRCLE } from '../constants/ActionTypes'

const initialState = {
  form_input: {
    circle_name: '',
    members: '',
    public_flg: '',
    circle_description: '',
    circle_image: ''
  },
  selected_circle_id: ''
}

function create_circle(state = initialState, action) {
  switch (action.type) {
    case CREATE_CIRCLE:
      return Object.assign({}, state, {
        form_input: {
          circle_name: action.circle_name
        }
      })

    default:
      return state;
  }
}

function select_circle(state = initialState, action) {
  switch (action.type) {
    case SELECT_CIRCLE:
      return Object.assign({}, state, {
        selected_circle_id: action.selected_circle_id
      })

    default:
      return state;
  }
}

export default function createReducer(obj) {
  return combineReducers(
    Object.assign({}, {
      create_circle,
      select_circle,
    }, obj)
  )
}
