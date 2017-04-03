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
      <li className="topicSearchList-item" key={ topic.id }>
        <Link to={ `/topics/${topic.id}/detail` }
              className="topicSearchList-item-link"
              onClick={ () => dispatch(setTopic(topic)) }>
          <AvatarsBox users={ topic.users } />
          <div className="topicSearchList-item-main">
            <div className="topicSearchList-item-main-header">
              <div className="topicSearchList-item-main-header-title oneline-ellipsis">
                { topic.display_title }
              </div>
              <div className="topicSearchList-item-main-header-count">
              </div>
            </div>
            <div className="topicSearchList-item-main-body oneline-ellipsis">
              { topic.latest_message.body }
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

export default connect()(Topic);
