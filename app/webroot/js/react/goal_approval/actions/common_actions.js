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

export function get(uri, success_callback, error_callback) {
  return axios.get(getBaseUrl() + uri, {
    timeout: 10000,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  })
  .then(success_callback)
  .catch(error_callback)

}
