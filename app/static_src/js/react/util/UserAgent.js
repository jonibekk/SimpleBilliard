import UaParser from "ua-parser-js";

// Define simply
// ES7 class properties is still under consideration(status「stage」), So it is not use
// Ref http://qiita.com/noriaki/items/e7adaaf440020fbf6836
const DEVICE_PC = "pc";
const DEVICE_TABLET = "tablet";
const DEVICE_SP = "sp";

export default class UserAgent {
  constructor() {
    this.parser = new UaParser();
  }

  getUA() {
    if (!this.ua) {
      this.ua = this.parser.getUA();
    }
    return this.ua;
  }

  /**
   *  Get user device type
   *  @return string
   */
  getDeviceType() {
    const ua = this.getUA();
    if (ua.indexOf('iPhone') > 0 || ua.indexOf('iPod') > 0 || ua.indexOf('Android') > 0 && ua.indexOf('Mobile') > 0) {
      return DEVICE_SP;
    } else if (ua.indexOf('iPad') > 0 || ua.indexOf('Android') > 0) {
      return DEVICE_TABLET;
    } else {
      return DEVICE_PC;
    }
  }

  /**
   *  Check PC
   *  @return boolean
   */
  isPc() {
    return this.getDeviceType() === DEVICE_PC;
  }

  /**
   * Check mobile app
   * @returns boolean
   */
  isMobileApp() {
    const ua = this.getUA();
    return ua.indexOf('Goalous App') >= 0;
  }


  /**
   * Check iOS app
   * @returns boolean
   */
  isIOSApp() {
    const ua = this.getUA();
    return ua.indexOf('Goalous App iOS') >= 0;
  }

}
