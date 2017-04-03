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
    this.focusSearchBox()

    // set keyword in previous searching
    const keyword = this.props.data.current_searching_keyword
    if (keyword) {
      ReactDOM.findDOMNode(this.refs.search).value = keyword
    }
  }

  fetchMoreSearch() {
    const next_url = this.props.data.next_url
    if (!next_url) {
      return
    }

    this.props.fetchMoreSearch(next_url)
  }

  inputKeyword(e) {
    const keyword = e.target.value
    this.props.inputKeyword(keyword)
  }

  focusSearchBox() {
    ReactDOM.findDOMNode(this.refs.search).focus()
  }

  onClickRemoveButton() {
    this.props.emptyTopics()
    this.focusSearchBox()
    this.emptySearchBox()
  }

  emptySearchBox() {
    ReactDOM.findDOMNode(this.refs.search).value = ''
  }

  render() {
    const { topics, fetching, current_searching_keyword } = this.props.data
    const render_topics = topics.map((topic) => {
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
              <div className="searchBox-remove-icon"
                   onClick={ this.onClickRemoveButton.bind(this) }>
                <i className="fa fa-remove"></i>
              </div>
              <input className="searchBox-input"
                     placeholder={__("Search topic")}
                     onChange={ this.inputKeyword.bind(this) }
                     ref="search" />
            </div>
          </div>
          <div className="topicSearchList-header-cancel">
            <a className="topicSearchList-header-cancel-button"
               onClick={ this.props.cancelSearchMode.bind(this) }>{__("Cancel")}</a>
          </div>
        </div>
        <ul>
          <InfiniteScroll
            loadingMore={ fetching }
            loadMore={ this.fetchMoreSearch.bind(this) }
            items={ render_topics }
            elementIsScrollable={ false }
            loader={ <Loading /> }
          />
          {
            current_searching_keyword && !fetching && topics.length == 0 &&
            <div className="topicSearchList-notFound">
              { __("Topics matching your search were not found") }
            </div>
          }
        </ul>
      </div>
    )
  }
}

TopicSearchList.defaultProps = {
  data: {
    topics: [],
    fetching: false
  }
}
