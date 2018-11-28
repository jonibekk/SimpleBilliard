import React from "react";
import {connect} from "react-redux";

class SearchItem extends React.Component {
  constructor(props) {
    super(props);
  }

  renderFromSearchAPI(item) {
    let username = '';
    let type_icon = '';
    let type_name = '';
    if (item.comment) {
      username = item.comment.user.display_username;
      type_icon = 'fa-comments';
      type_name = __('Comments');
    } else {
      username = item.post.user.display_username;
      type_icon = item.post.type == 3 ? 'fa-check-circle' : 'fa-comment-o';
      type_name = item.post.type == 3 ? __('Actions'):__('Posts');
    }

    return (
      <div className="searchPage-item">
        <a className="searchPage-item-link saved-item-click-target" href={`/post_permanent/${item.id}?back=true`} target="_blank">
          <div className="searchPage-item-imgWrapper">
            <img src={item.img_url} className="lazy"/>
          </div>
          <div className="searchPage-item-main">
            <div className="searchPage-item-main-body" dangerouslySetInnerHTML={{__html: item.highlight}}>
            </div>
            <div className="searchPage-item-main-footer">
              <span className="searchPage-item-main-footer-left">
                {username}
              </span>
              <div className="searchPage-item-main-footer-right">
                <p className="mr_8px">
                  <i className={`fa mr_2px ${type_icon}`}></i>
                  {type_name}
                </p>
                <span>{item.display_created}</span>
              </div>
            </div>
          </div>
        </a>
      </div>
    )
  }

  renderFromOldSearch(item) {
    let type = item.type
    let url = ""
    if (type === "circles") {
      url = "/circle_feed/" + item.id
    } else {
      url = "/users/view_goals/user_id:" + item.id
    }
    return (
      <div className="searchPage-item">
        <a className="searchPage-item-link saved-item-click-target" href={url} target="_blank">
          <div className="searchPage-item-imgWrapper">
            <img src={item.image} className="lazy"/>
          </div>
          <div className="searchPage-item-main">
            <div className="searchPage-item-main-body">
              {item.text}
            </div>
          </div>
        </a>
      </div>
    )
  }

  render() {
    const {item} = this.props
    if (!item) {
      return null;
    }

    if (!item.type) {
      return this.renderFromSearchAPI(item);
    } else {
      return this.renderFromOldSearch(item);
    }
  }
}

SearchItem.propTypes = {
  item: React.PropTypes.object,
}

export default connect()(SearchItem);
