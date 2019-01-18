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
  if (!cake.is_mb_app) return {};
  let layout = {
    header_top: PositionMobileApp.HEADER_TOP,
    body_top: PositionMobileApp.BODY_TOP,
    body_bottom: PositionMobileApp.BODY_BOTTOM,
    footer_bottom: PositionMobileApp.FOOTER_BOTTOM
  };
  if (!cake.is_mb_app_web_footer) {
    return layout;
  }

  const footerEl = document.getElementsByClassName('mobile-app-footer')[0];
  if (!footerEl) {
    return layout;
  }
  // If mobile app footer is native, change position to fit layout
  const mobileAppFooterHeight = footerEl.clientHeight;
  console.log({mobileAppFooterHeight});
  layout.body_bottom += mobileAppFooterHeight;
  layout.footer_bottom += mobileAppFooterHeight;
  return layout;
}
