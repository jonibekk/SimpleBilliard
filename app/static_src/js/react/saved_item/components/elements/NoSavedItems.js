import React from "react";

export default class NoSavedItems extends React.Component {
  render() {
    return (
      <div className="panel-block savedItemList-none">
        <div className="savedItemList-none-kv">
          <i className="fa fa-bookmark-o"></i>
          <p>{__("Save item")}</p>
        </div>
        <p className="savedItemList-none-description">
          {__("Save Actions and Posts that you want to see again. No one is notified, and only you can see what you’ve saved.")}
        </p>
      </div>
    )
  }
}

