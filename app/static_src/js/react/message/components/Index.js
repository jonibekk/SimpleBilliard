import React from "react"
import TopicList from "./elements/index/TopicList"
import TopicSearchList from "./elements/search/TopicList"

export default class Index extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { is_search_mode } = this.props.index
    return (
      <div>
        {(() => {
          if (!is_search_mode) {
            return (
              <TopicList data={ this.props.index }
                         fetchInitData={ this.props.fetchInitData }
                         fetchMore={ (url) => this.props.fetchMore(url) }
                         changeToSearchMode={ this.props.changeToSearchMode } />
            )
          } else {
            return (
              <TopicSearchList data={ this.props.search }
                               fetchMoreSearch={ (url) => this.props.fetchMoreSearch(url) }
                               inputKeyword={ (keyword) => this.props.inputKeyword(keyword) }
                               changeToIndexMode={ this.props.changeToIndexMode }
                               emptyTopics={ this.props.emptyTopics} />
            )
          }
        })()}
      </div>
    )
  }
}
