import validator from 'validator'
import * as types from '../constants/ActionTypes'

export function _checkValue(target) {
  const name = target.name
  const val = target.value

  switch (name) {
    case 'password':
      const pattern = /^(?=.*?[0-9])(?=.*?[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{8,50}$/

      if(val === '' || !validator.isLength(val, { min: 8 })) {
        return invalid(name, cake.message.validate.signup_password_min_length)
      } else if(val === '' || !validator.isLength(val, { min: 8, max: 50 })) {
        return invalid(name, cake.message.validate.signup_password_max_length)
      } else if(!validator.matches(val, pattern)) {
        return invalid(name, cake.message.validate.signup_password_alpha_num_required)
      }
      return valid(name)
    case 'team_name':
      if(val === '' || !validator.isLength(val, { min: 0, max: 128 })) {
        return invalid(name, cake.message.validate.signup_team_name_length)
      }
      return valid(name)
    case 'first_name':
      if(val === '' || !validator.isLength(val, { min: 0, max: 128 })) {
        return invalid(name, cake.message.validate.signup_user_name_length)
      }else if(!validator.isAlpha(val)) {
        return invalid(name, cake.message.validate.signup_user_name_alpha)
      }
      return valid(name)
    case 'last_name':
      if(val === '' || !validator.isLength(val, { min: 0, max: 128 })) {
        return invalid(name, cake.message.validate.signup_user_name_length)
      }else if(!validator.isAlpha(val)) {
        return invalid(name, cake.message.validate.signup_user_name_alpha)
      }
      return valid(name)
    case 'birth_year':
      if(!val) {
        status.type = types.INVALID
        status.data[name].invalid = true
        status.data[name].message = cake.message.validate.signup_birth_day_required
      }
      return status
    case 'birth_month':
      if(!val) {
        return invalid(name, cake.message.validate.signup_birth_day_required)
      }
      return valid(name)
    case 'birth_day':
      if(!val) {
        return invalid(name, cake.message.validate.signup_birth_day_required)
      }
      return valid(name)
    case 'privacy_policy':
      if(!target.checked) {
        return invalid(name, cake.message.validate.signup_privacy_policy_required)
      }
      return valid(name)
  }
}

export function invalid(name, message) {
  return {
    type: types.INVALID,
    data: {
      [name]: {
        invalid: true,
        message
      }
    }
  }
}

export function valid(name) {
  return {
    type: types.VALID,
    data: {
      [name]: {
        invalid: false,
        message: ''
      }
    }
  }
}
