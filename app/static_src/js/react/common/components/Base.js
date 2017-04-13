import React from "react";

export default class Base extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      enabled_leave_page_alert: true,
      leave_page_alert_msg: cake.message.notice.a
    }
  }


  componentDidMount() {
    window.addEventListener("beforeunload", this.onBeforeUnloadHandler.bind(this))
  }

  componentWillUnmount() {
    this.removeBeforeUnloadHandler()
  }

  removeBeforeUnloadHandler() {
    window.removeEventListener("beforeunload", this.onBeforeUnloadHandler.bind(this))
  }

  onBeforeUnloadHandler(event) {
    if (this.state.enabled_leave_page_alert) {
      return event.returnValue = this.state.leave_page_alert_msg
    }
  }
}
