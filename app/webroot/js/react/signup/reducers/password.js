import {
  INPUT_PASSWORD,
  PASSWORD_IS_INVALID,
  CHECKING_PASSWORD
} from '../constants/ActionTypes'

const initialState = {
  inputed_password: '',
  checking_password: false,
  password_is_invalid: false,
  invalid_message: ''
}

export default function password(state = initialState, action) {
  switch (action.type) {
    case INPUT_PASSWORD:
      return Object.assign({}, state, {
        inputed_password: action.inputed_password
      })
    case PASSWORD_IS_INVALID:
      return Object.assign({}, state, {
        password_is_invalid: true,
        invalid_message: action.invalid_message
      })
    case CHECKING_PASSWORD:
      return Object.assign({}, state, {
        checking_password: true
      })
    default:
      return state;
  }
}
