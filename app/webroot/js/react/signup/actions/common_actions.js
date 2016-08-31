import axios from 'axios'
import validator from 'validator'

export function getBaseUrl() {
  if (typeof cake === "undefined") {
    return 'http://localhost'
  } else {
    return cake.url.route_url
  }
}

export function getCsrfTokenKey() {
  if (typeof cake === "undefined") {
    return 'csrf_token_key'
  } else {
    return cake.data.csrf_token.key
  }
}

export function post(uri, data, success_callback, error_callback) {

  const csrf_token_key = getCsrfTokenKey()
  const post_data = Object.assign({
    'data[_Token][key]': csrf_token_key
  }, data)
  const base_url = getBaseUrl()
  const form_data = new FormData()

  for (const key in post_data) {
    form_data.append(key, post_data[key])
  }
  return axios.post(base_url + uri, form_data, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json',
    contentType: 'application/json'
  })
  .then(success_callback)
  .catch(error_callback)
}

export function mapValidationMsg(before_mapped_messages) {
  const result = {}
  const map = {
    'data[Team][name]': 'team_name',
    'data[Team][timezone]': 'timezone',
    'data[Team][start_term_month]': 'start_month',
    'data[Team][border_months]': 'term',
    'data[User][password]': 'password',
    'data[User][first_name]': 'first_name',
    'data[User][last_name]': 'last_name',
    'data[User][local_date]': 'local_date',
    'data[User][birth_day]': 'birth_day'
  }

  for (const key in map) {
    result[map[key]] = before_mapped_messages[key]
  }
  return result
}

export function getLocalDate() {
  const date = new Date();
  const year = date.getFullYear();
  const month = date.getMonth() + 1;
  const day = date.getDate();
  const hours = date.getHours();
  const minutes = date.getMinutes();
  const seconds = date.getSeconds();
  const fullDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`

  return fullDate;
}

export function range(start, end) {
  const len = end - start
  const a = new Array(len)

  for (let i=0, c=start; i<len; i++, c++) a[i] = c
  return a
}

export function _checkValue(target) {
  const name = target.name
  const val = target.value
  const status = { name, error: false, messages: {} }

  switch (name) {
    case 'password':
      const pattern = /^(?=.*?[0-9])(?=.*?[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{8,50}$/

      if(val === '' || !validator.isLength(val, { min: 8 })) {
        status.error = true
        status.messages.password = cake.message.validate.signup_password_min_length
      } else if(val === '' || !validator.isLength(val, { min: 8, max: 50 })) {
        status.error = true
        status.messages.password = cake.message.validate.signup_password_max_length
      } else if(!validator.matches(val, pattern)) {
        status.error = true
        status.messages.password = cake.message.validate.signup_password_alpha_num_required
      }
      return status
    case 'team_name':
      if(val === '' || !validator.isLength(val, { min: 0, max: 128 })) {
        status.error = true
        status.messages.team_name = cake.message.validate.signup_team_name_length
      }
      return status
    case 'first_name':
      if(val === '' || !validator.isLength(val, { min: 0, max: 128 })) {
        status.error = true
        status.messages.first_name = cake.message.validate.signup_user_name_length
      }else if(!validator.isAlpha(val)) {
        status.error = true
        status.messages.first_name = cake.message.validate.signup_user_name_alpha
      }
      return status
    case 'last_name':
      if(val === '' || !validator.isLength(val, { min: 0, max: 128 })) {
        status.error = true
        status.messages.last_name = cake.message.validate.signup_user_name_length
      }else if(!validator.isAlpha(val)) {
        status.error = true
        status.messages.last_name = cake.message.validate.signup_user_name_alpha
      }
      return status
    case 'birth_year':
      if(!val) {
        status.error = true
        status.messages.birth_day = cake.message.validate.signup_birth_day_required
      }
      return status
    case 'birth_month':
      if(!val) {
        status.error = true
        status.messages.birth_day = cake.message.validate.signup_birth_day_required
      }
      return status
    case 'birth_day':
      if(!val) {
        status.error = true
        status.messages.birth_day = cake.message.validate.signup_birth_day_required
      }
      return status
    case 'privacy_policy':
      if(!target.checked) {
        status.error = true
        status.messages.privacy_policy = cake.message.validate.signup_privacy_policy_required
      }
      return status
    default:
      return status
  }

  return status
}
