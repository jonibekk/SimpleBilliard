import React from "react";
import {connect} from "react-redux";

class SearchItem extends React.Component {
  constructor(props) {
    super(props);
  }

  renderPostOrAction(item) {
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

  renderCircle(item) {
    return (
      <div className="searchPage-item">
        <a className="searchPage-item-link saved-item-click-target" href={"/circle_feed/" + item.id} target="_blank">
          <div className="searchPage-item-imgWrapper">
            <img src={item.img_url} className="lazy"/>
          </div>
          <div className="searchPage-item-main">
            <div className="searchPage-item-main-body">
              {item.circle.name}
            </div>
            <div className="searchPage-item-main-footer">
              <div className="searchPage-item-main-footer-left">
                <span className="mr_8px">
                  <i className={`fa mr_2px fa-user`}></i>
                  {item.circle.circle_member_count}
                </span>
                <span className="mr_8px">
                  <i className={`fa mr_2px ${item.circle.public_flg ? "fa-unlock" : "fa-lock"}`}></i>
                  {item.circle.public_flg ? cake.word.public : cake.word.secret }
                </span>
              </div>
              <div className="searchPage-item-main-footer-right">
                <span>{item.display_last_post_created}</span>
              </div>
            </div>
          </div>
        </a>
      </div>
    )
  }

  renderUser(item) {
    const inactiveCssClass = !item.is_active ? " searchPage-item-link-inactive" : "";
    return (
      <div className="searchPage-item">
        <a
          className={"searchPage-item-link saved-item-click-target" + inactiveCssClass}
          href={"/users/view_goals/user_id:" + item.id} target="_blank">
          <div className="searchPage-item-imgWrapper">
            <img src={item.img_url} className="lazy"/>
          </div>
          <div className="searchPage-item-main">
            <div className="searchPage-item-main-body">
              {item.user.display_username}
            </div>
          </div>
        </a>
      </div>
    )
  }

  dateFormat(timestamp) {
    const d = new Date(timestamp * 1000)
    if (cake.lang === "jpn") {
      return d.getFullYear() + "年" + (d.getMonth() + 1) + "月" + d.getDate() + "日";
    }
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
      "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];
    return monthNames[d.getMonth()] + " " + d.getDate() + " " + d.getFullYear();
  }

  render() {
    const {item, type} = this.props
    if (!item) {
      return null;
    }

    switch (type) {
      case "circle_post":
      case "action":
        return this.renderPostOrAction(item);
      case "users":
        return this.renderUser(item);
      case "circles":
        return this.renderCircle(item);
    }
  }
}

SearchItem.propTypes = {
  item: React.PropTypes.object,
}

export default connect()(SearchItem);
