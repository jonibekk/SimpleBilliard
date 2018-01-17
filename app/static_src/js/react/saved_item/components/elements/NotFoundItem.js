import React from "react";

export default class NotFoundItem extends React.Component {
  render() {
    return (
      <div className="panel-block">
        <p className="savedItemList-notFound">
          {__("Item not found")}
        </p>
      </div>
    )
  }
}

