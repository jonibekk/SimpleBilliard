import axios from 'axios'
import FormData from 'form-data'

export function getBaseUrl() {
  // テストにおけるモックのURLを定義
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
    'data[User][birth_day]': 'birth_day',
    'data[User][privacy_policy]': 'privacy_policy',
    'data[User][age]': 'age'
  }

  for (const key in map) {
    if(typeof before_mapped_messages[key] !== "undefined") {
      result[map[key]] = before_mapped_messages[key]
    }
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
