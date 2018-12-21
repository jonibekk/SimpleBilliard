import React from "react";
import AvatarsBox from "~/common/components/AvatarsBox";
import {setTopicOnDetail} from "~/message/actions/search";
import {emptyTopicList} from "~/message/actions/index";
import {Link} from "react-router";
import {connect} from "react-redux";

class Message extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      is_taped_item: false,
    }
  }

  onClickLinkToDetail() {
    const {dispatch, topic} = this.props
    dispatch(setTopicOnDetail(topic))
    dispatch(emptyTopicList())
  }

  tapLink(e) {
    this.setState({is_taped_item: true})
  }

  render() {
    const topic = this.props.topic;
    const type = this.props.type;
    return (
      <li className="topicSearchList-item" key={ topic.id }>
        <Link to={ `/topics/${topic.id}/detail` }
              className={`topicSearchList-item-link ${this.state.is_taped_item ? "is-hover" : ""}`}
              onClick={ this.onClickLinkToDetail.bind(this) }
              onTouchTap={ this.tapLink.bind(this) }>
          <AvatarsBox users={ topic.users }/>
          <div className="topicSearchList-item-main">
            <div className="topicSearchList-item-main-header">
              <div className="topicSearchList-item-main-header-title oneline-ellipsis">
                { topic.display_title }
              </div>
              <div className="topicSearchList-item-main-header-count">
                { (topic.members_count > 2 || topic.title) && `(${topic.members_count})` }
              </div>
            </div>
            <div className="topicSearchList-item-main-body oneline-ellipsis">
                { type === 'message' ? `${topic.hit_message_count} messages hit` : `${topic.matching_member_count} matching_members` }
            </div>
          </div>
          <div className="topicSearchList-item-right">
            { topic.latest_message.display_created }
          </div>
        </Link>
      </li>
    )
  }
}

export default connect()(Message);

