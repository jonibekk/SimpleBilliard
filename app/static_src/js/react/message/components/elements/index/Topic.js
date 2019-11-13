import React from "react";
import AvatarsBox from "~/common/components/AvatarsBox";
import {setTopicOnDetail} from "~/message/actions/search";
import {emptyTopicList} from "~/message/actions/index";
import {Link} from "react-router";
import {connect} from "react-redux";

class Topic extends React.Component {

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
    const topic = this.props.topic
    const latest_message = topic.latest_message || {};

    return (
      <li className="topicList-item" key={ topic.id }>
        <Link to={ `/topics/${topic.id}/detail` }
              className={`topicList-item-link ${topic.is_unread ? 'is-unread' : ''} ${this.state.is_taped_item ? "is-hover" : ""}`}
              onClick={ this.onClickLinkToDetail.bind(this) }
              onTouchTap={ this.tapLink.bind(this) }>
          <AvatarsBox users={ topic.users } />
          <div className="topicList-item-main">
            <div className="topicList-item-main-header">
              <div className={`topicList-item-main-header-title oneline-ellipsis ${topic.is_unread ? 'mod-bold' : ''}` }>
                <span>
                  { topic.display_title }
                </span>
              </div>
              <div className="topicList-item-main-header-count">
                { (topic.members_count > 2 || topic.title) && `(${topic.members_count})` }
              </div>
            </div>
            <div className="topicList-item-main-body oneline-ellipsis">
              { latest_message.body || '' }
            </div>
            <div className="topicList-item-main-footer">
              {(() => {
                if ((topic.members_count - 1) == topic.read_count) {
                  return <span><i className="fa fa-check is-read"></i> </span>
                } else if (topic.members_count == 2) {
                  // if member number is two and no read, display nothing.
                  return null
                } else {
                  return <span><i className="fa fa-check"></i> { topic.read_count }・</span>
                }
              })()}
              { latest_message.display_created || '' }
            </div>
          </div>
        </Link>
      </li>
    )
  }
}

export default connect()(Topic);
