import axios from 'axios'

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

  return axios.post(base_url + uri, post_data, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  })
  .then(success_callback)
  .catch(error_callback)
}

export function mapValidationMsg(before_mapped_messages) {
  let result = {}
  const map = {
    'data[Team][name]': 'team_name',
    'data[Team][timezone]': 'timezone',
    'data[Team][start_term_month]': 'start_month',
    'data[Team][border_months]': 'term',
    'data[User][password]': 'password',
    'data[User][first_name]': 'first_name',
    'data[User][last_name]': 'last_name',
    'data[User][local_date]': 'local_date'
  }

  Object.keys(map).forEach(key => {
    if (before_mapped_messages[key]) {
      result[map[key]] = before_mapped_messages[key]
    }
  }, map)
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
