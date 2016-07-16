import axios from 'axios'

export function getBaseUrl() {
  if(typeof cake === "undefined") {
    return 'http://localhost'
  } else {
    return cake.url.route_url
  }
}

export function post(uri, data, success_callback, error_callback) {
  const post_data = Object.assign({
    'data[_Token][key]': 'csrf_token_key' //cake.data.csrf_token.key,
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
    'data[User][password]': 'password',
    'data[User][first_name]': 'first_name',
    'data[User][last_name]': 'last_name',
    'data[Local][first_name]': 'local_first_name',
    'data[Local][last_name]': 'local_last_name'
  }

  Object.keys(map).forEach(key => {
    if (before_mapped_messages[key]) {
      result[map[key]] = before_mapped_messages[key]
    }
  }, map)
  return result
}
