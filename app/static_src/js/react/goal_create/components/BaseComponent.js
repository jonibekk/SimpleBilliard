import React from "react";

export default class BaseComponent extends React.Component {
  componentDidMount() {
    window.addEventListener("beforeunload", this.onBeforeUnloadHandler)
  }

  componentWillUnmount() {
    this.removeBeforeUnloadHandler()
  }

  removeBeforeUnloadHandler() {
    window.removeEventListener("beforeunload", this.onBeforeUnloadHandler)
  }

  onBeforeUnloadHandler(event) {
    return event.returnValue = cake.message.notice.a
  }
}
