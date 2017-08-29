import React from "react";

export default class LoadingButton extends React.Component {
  render() {
    return (
      <span className={`btn btnRadiusOnlyIcon mod-active ${this.props.class}`}>
        <i className="fa fa-circle-o-notch fa-spin"></i>
      </span>
    )
  }
}
