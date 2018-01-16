import React from "react";
import {connect} from "react-redux";
import * as actions from "~/saved_item/actions/index";
import saved_item from "../../reducers/saved_item";

class SavedItem extends React.Component {
  constructor(props) {
    super(props);
    this.unsave = this.unsave.bind(this)
  }

  unsave(e, saved_item) {
    e.stopPropagation();
    e.preventDefault()
    this.props.dispatch(
      actions.unsave(saved_item)
    )
  }

  render() {
    const {saved_item} = this.props
    if (!saved_item) {
      return null;
    }

    return (
      <li className="savedItemList-item">
        <a className="savedItemList-item-link saved-item-click-target" href={`/post_permanent/${saved_item.post_id}`} target="_blank">
          <div className="avatorsBox">
            <div className="avatorsBox-one">
              <img src={saved_item.image_url} className="lazy"/>
            </div>
          </div>
          <div className="savedItemList-item-main">
            <div className="savedItemList-item-main-body">
              <i className={`fa ${saved_item.type == 3 ? 'fa-check-circle' : 'fa-comment-o'}`}></i>
              {saved_item.body.substr(0, 100)}
            </div>
            <div className="savedItemList-item-main-footer">
                    <span className="savedItemList-item-main-footer-left">
              {saved_item.post_user.display_username}
                    </span>&nbsp;
              <span className="savedItemList-item-main-footer-right">
                {saved_item.display_created}
                    </span>
            </div>
          </div>
          <div className="">
            <div role="group" className="">
              <p className="dropdown-toggle" data-toggle="dropdown" role="button"
                 aria-expanded="false">
                <i className="fa fa-ellipsis-h savedItemList-item-option-icon"></i>
              </p>
              <ul className="dropdown-menu pull-right" role="menu">
                <li key={`saved_item_${saved_item.id}_menu`} onClick={(e) => this.unsave(e, saved_item)}>
                  <span className="dropdown-menu-item">
                    {__("Unsave")}
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </a>
      </li>
    )
  }
}

SavedItem.propTypes = {
  saved_item: React.PropTypes.object,
}

export default connect()(SavedItem);
