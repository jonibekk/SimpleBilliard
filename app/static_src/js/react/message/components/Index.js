import React from "react"
import TopicList from "./elements/TopicList"
import TopicSearchList from "./elements/TopicSearchList"

export default class Index extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { topics, fetching_topics, next_url, is_search_mode } = this.props.index
    return (
      <div>
        {(() => {
          if (is_search_mode) {
            return (
              <TopicSearchList cancelSearchMode={ this.props.cancelSearchMode } />
            )
          } else {
            return (
              <TopicList topics={ topics }
                         fetching_topics= { fetching_topics }
                         next_url={ next_url }
                         fetchInitData={ this.props.fetchInitData }
                         fetchMoreTopics={ (url) => this.props.fetchMoreTopics(url) }
                         changeToSearchMode={ this.props.changeToSearchMode } />
            )
          }
        })()}
      </div>
    )
  }
}
