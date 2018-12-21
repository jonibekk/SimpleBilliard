import {isIOSApp, isMobileApp, isOldIOSApp} from "~/util/base";
import {PositionIOSApp, PositionMobileApp} from "~/message/constants/Styles";
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

export function getLayout() {
  if (isOldIOSApp()) {
    return {
      header_top: PositionIOSApp.HEADER_TOP,
      body_top: PositionIOSApp.BODY_TOP,
      body_bottom: PositionIOSApp.BODY_BOTTOM,
      footer_bottom: PositionIOSApp.FOOTER_BOTTOM
    }
  } else if (isMobileApp()) {
    return {
      header_top: PositionMobileApp.HEADER_TOP,
      body_top: PositionMobileApp.BODY_TOP,
      body_bottom: PositionMobileApp.BODY_BOTTOM,
      footer_bottom: PositionMobileApp.FOOTER_BOTTOM
    }
  }
  return {};
}
