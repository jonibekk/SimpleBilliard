import React from "react";

export default class LoadingButton extends React.Component {
  render() {
    return (
      <span className="btn btnRadiusOnlyIcon mod-active">
        <i className="fa fa-circle-o-notch fa-spin"></i>
      </span>
    )
  }
}
