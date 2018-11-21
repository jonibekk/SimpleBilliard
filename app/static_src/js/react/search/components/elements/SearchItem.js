import React from "react";
import {connect} from "react-redux";

class SearchItem extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const {item} = this.props
    if (!item) {
      return null;
    }

    return (
      <div className="searchPage-item">
        <a className="searchPage-item-link saved-item-click-target" href='{`/post_permanent/${item.post_id}?back=true`}' target="_blank">
          <div className="searchPage-item-imgWrapper">
            <img src={item.image_url} className="lazy"/>
          </div>
          <div className="searchPage-item-main">
            <div className="searchPage-item-main-body">
              {item.body.substr(0, 100)}
            </div>
            <div className="searchPage-item-main-footer">
              <span className="searchPage-item-main-footer-left">
                {item.post_user.display_username}
              </span>
              <div className="searchPage-item-main-footer-right">
                <p className="mr_8px"><i className={`fa mr_2px ${item.type == 3 ? 'fa-check-circle' : 'fa-comment-o'}`}></i>投稿</p>
                <span>{item.display_created}</span>
              </div>
            </div>
          </div>
        </a>
      </div>
    )
  }
}

SearchItem.propTypes = {
  item: React.PropTypes.object,
}

export default connect()(SearchItem);
