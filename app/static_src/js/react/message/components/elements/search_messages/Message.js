import React from "react";
import AvatarsBox from "~/common/components/AvatarsBox";
import {emptyTopicList} from "~/message/actions/index";
import {browserHistory, Link} from "react-router";
import {connect} from "react-redux";
import {setTopicOnDetail} from "~/message/actions/search";

class Message extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      is_taped_item: false,
    }
  }

  onClickLinkToDetail(base_url, query_string) {
    const {dispatch, item} = this.props;
    browserHistory.push({pathname: base_url, search: query_string, state: {back_url: location.href}});
    dispatch(setTopicOnDetail(item.topic))
    dispatch(emptyTopicList())
  }

  tapLink(e) {
    this.setState({is_taped_item: true})
  }

  render() {
    const item = this.props.item;
    const userImages = [
      {
        id: item.message.sender.id,
        profile_img_url: {
          medium_large: item.img_url
        }
      }
    ];

    return (
      <li className="topicSearchList-item" key={ item.message.id }>
        <div
          className={`topicSearchList-item-link ${this.state.is_taped_item ? "is-hover" : ""}`}
          onClick={ this.onClickLinkToDetail.bind(this, `/topics/${item.topic.id}/detail`, `?message_id=${item.message.id}`) }
          onTouchTap={ this.tapLink.bind(this) }>
          <AvatarsBox users={ userImages } key={item.message.id} />
          <div className="topicSearchList-item-main">
            <div className="topicSearchList-item-main-header">
              <div className="topicSearchList-item-main-header-title oneline-ellipsis">
                { item.message.sender.display_username }
              </div>
            </div>
            <div className="topicSearchList-item-main-body oneline-ellipsis" dangerouslySetInnerHTML={{__html: item.highlight}}>
            </div>
          </div>
          <div className="topicSearchList-item-right">
            { item.message.display_created }
          </div>
        </div>
      </li>
    )
  }
}

export default connect()(Message);

