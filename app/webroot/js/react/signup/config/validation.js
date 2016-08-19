export const validation_settings = {
  password: {
    pattern: /^(?=.*?[0-9])(?=.*?[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{8,50}$/,
    message: __("Password is incorrect.")
  }
}
