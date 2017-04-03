import React from 'react'
import AvatarsBox from "~/common/components/AvatarsBox"
import { setTopic } from '~/message/actions/search'
import { Link } from "react-router"
import { connect } from "react-redux"

class Topic extends React.Component {
  render() {
    const topic = this.props.topic
    const { dispatch } = this.props
    return (
      <li className="topicList-item" key={ topic.id }>
        <Link to={ `/topics/${topic.id}/detail` }
              className={`topicList-item-link ${topic.is_unread ? 'is-unread' : ''}`}
              onClick={ () => dispatch(setTopic(topic)) }>
          <AvatarsBox users={ topic.users } />
          <div className="topicList-item-main">
            <div className="topicList-item-main-header">
              <div className="topicList-item-main-header-title oneline-ellipsis">
                <span>
                  { topic.display_title }
                </span>
              </div>
              <div className="topicList-item-main-header-count">
                { topic.members_count > 2 && `(${topic.members_count})` }
              </div>
            </div>
            <div className="topicList-item-main-body oneline-ellipsis">
              { topic.latest_message.body }
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
              { topic.latest_message.display_created }
            </div>
          </div>
        </Link>
      </li>
    )
  }
}

export default connect()(Topic);
