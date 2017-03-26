import React from 'react'
import ReactDOM from 'react-dom'
import InfiniteScroll from "redux-infinite-scroll"
import Loading from "~/message/components/elements/index/Loading"
import Topic from "./Topic"

export default class TopicSearchList extends React.Component {
  constructor(props) {
    super(props);
  }

  componentDidMount() {
    ReactDOM.findDOMNode(this.refs.search).focus()
  }

  fetchMoreSearchTopics() {
    const next_search_url = this.props.next_search_url
    if (!next_search_url) {
      return
    }

    this.props.fetchMoreSearchTopics(next_search_url)
  }

  inputSearchKeyword(e) {
    const keyword = e.target.value
    this.props.inputSearchKeyword(keyword)
  }

  render() {
    const { topics_searched, searching_topics, inputed_search_keyword } = this.props
    const render_topics = topics_searched.map((topic) => {
      return (
        <Topic topic={ topic }
               key={ topic.id } />
      )
    })

    return (
      <div className="panel panel-default topicSearchList">
        <div className="topicSearchList-header">
          <div className="topicSearchList-header-searchBox">
            <div className="searchBox">
              <div className="searchBox-search-icon">
                <i className="fa fa-search"></i>
              </div>
              <div className="searchBox-remove-icon">
                <i className="fa fa-remove"></i>
              </div>
              <input className="searchBox-input"
                     placeholder={__("Search topic")}
                     onChange={ this.inputSearchKeyword.bind(this) }
                     ref="search"
                     defaultValue={ inputed_search_keyword } />
            </div>
          </div>
          <div className="topicSearchList-header-cancel">
            <a className="topicSearchList-header-cancel-button"
               onClick={ this.props.cancelSearchMode.bind(this) }>{__("Cancel")}</a>
          </div>
        </div>
        <ul>
          <InfiniteScroll
            loadingMore={ searching_topics }
            loadMore={ this.fetchMoreSearchTopics.bind(this) }
            items={ render_topics }
            elementIsScrollable={ false }
            loader=""
          />
        </ul>
        { searching_topics && <Loading />}
      </div>
    )
  }
}

TopicSearchList.defaultProps = {
  topics_searched: [],
  searching_topics: false,
  inputed_search_keyword:''
}
