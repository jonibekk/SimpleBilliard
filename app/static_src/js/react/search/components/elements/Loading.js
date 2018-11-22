import React from "react";

export default class Loading extends React.Component {
  render() {
    return (
      <div className="panel-block bd-b-sc4">
        <div className="text-align_c">
          <img src="/img/ajax-loader.gif" width="16" height="16"/>
        </div>
      </div>
    )
  }
}

