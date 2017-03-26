import React from "react"
import TopicList from "./elements/index/TopicList"
import TopicSearchList from "./elements/index/search/TopicList"

export default class Index extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { topics, topics_searched, fetching_topics, searching_topics, next_url, next_search_url, is_search_mode, inputed_search_keyword } = this.props.index
    return (
      <div>
        {(() => {
          if (is_search_mode) {
            return (
              <TopicSearchList inputed_search_keyword={ inputed_search_keyword }
                               searching_topics={ searching_topics }
                               next_search_url={ next_search_url }
                               topics_searched={ topics_searched }
                               cancelSearchMode={ this.props.cancelSearchMode }
                               fetchMoreSearchTopics={ (url) => this.props.fetchMoreSearchTopics(url) }
                               inputSearchKeyword={ this.props.inputSearchKeyword } />
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
