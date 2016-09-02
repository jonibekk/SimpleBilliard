import validator from 'validator'
import * as types from '../constants/ActionTypes'

export function _checkValue(target) {
  const name = target.name
  const val = target.value
  const status = { type: types.VALID, data: { [name]: { invalid: false, message: '' } } }

  switch (name) {
    case 'password':
      const pattern = /^(?=.*?[0-9])(?=.*?[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{8,50}$/

      if(val === '' || !validator.isLength(val, { min: 8 })) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_password_min_length
      } else if(val === '' || !validator.isLength(val, { min: 8, max: 50 })) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_password_max_length
      } else if(!validator.matches(val, pattern)) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_password_alpha_num_required
      }
      return status
    case 'team_name':
      if(val === '' || !validator.isLength(val, { min: 0, max: 128 })) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_team_name_length
      }
      return status
    case 'first_name':
      if(val === '' || !validator.isLength(val, { min: 0, max: 128 })) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_user_name_length
      }else if(!validator.isAlpha(val)) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_user_name_alpha
      }
      return status
    case 'last_name':
      if(val === '' || !validator.isLength(val, { min: 0, max: 128 })) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_user_name_length
      }else if(!validator.isAlpha(val)) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_user_name_alpha
      }
      return status
    case 'birth_year':
      if(!val) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_birth_day_required
      }
      return status
    case 'birth_month':
      if(!val) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_birth_day_required
      }
      return status
    case 'birth_day':
      if(!val) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_birth_day_required
      }
      return status
    case 'privacy_policy':
      if(!target.checked) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_privacy_policy_required
      }
      return status
    default:
      return status
  }

  return status
}

export function valid(name, message) {
  return {

  }
}

export function invalid(name) {
  return {
    
  }
}
