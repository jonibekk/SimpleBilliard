/**
 * Get summary error message
 * @param response
 * @returns {string}
 */
export function getErrMsg(response) {
  if (!response.data.validation_errors) {
    return response.data.message;
  }
  const {validation_errors} = response.data;
  if (Object.keys(validation_errors).length == 0) {
    return response.data.message;
  }

  let msg = "";
  Object.keys(validation_errors).forEach(function(key) {
    msg +=  validation_errors[key] + "\n";
  });
  return msg;
}
