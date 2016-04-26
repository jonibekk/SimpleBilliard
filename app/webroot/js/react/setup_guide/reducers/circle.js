import { combineReducers } from 'redux'
import { CREATE_CIRCLE, SELECT_CIRCLE, FETCH_CIRCLES, JOIN_CIRCLE } from '../constants/ActionTypes'

const initialState = {
  form_input: {
    circle_name: '',
    members: '',
    public_flg: '',
    circle_description: '',
    circle_image: ''
  },
  selected_circle_id: '',
  circles: [],
  joined: false
}

function create_circle(state = initialState, action) {
  switch (action.type) {
    case CREATE_CIRCLE:
      return Object.assign({}, state, {
        form_input: action.form_input
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

function fetch_circles(state = initialState, action) {
  switch (action.type) {
    case FETCH_CIRCLES:
      return Object.assign({}, state, {
        circles: action.circles
      })
    default:
      return state
  }
}

function joinCircle(state = initialState, action) {
  switch (action.types) {
    case JOIN_CIRCLE:
      return Object.assign({}, state, {
        joined: true
      })
    default:
      return state
  }
}

function createReducer(obj) {
  return combineReducers(
    Object.assign({}, {
      create_circle,
      select_circle,
      fetch_circles
    }, obj)
  )
}
