import React from "react"
import Topic from "./elements/Topic"
import InfiniteScroll from "redux-infinite-scroll"
import Loading from "~/message/components/elements/LoadingTopics"

export default class Index extends React.Component {
  constructor(props) {
    super(props);
  }

  componentWillMount() {
    this.props.fetchInitData()
  }

  fetchMoreTopics() {
    const next_url = this.props.index.next_url
    if (!next_url) {
      return
    }

    this.props.fetchMoreTopics(next_url)
  }

  render() {
    const { topics, fetching_topics } = this.props.index
    const render_topics = topics.map((topic) => {
      return (
        <Topic topic={ topic }
               key={ topic.id } />
      )
    })

    return (
      <div className="panel panel-default topicList">
        <div className="topicList-header">
          <div className="searchBox">
            <div className="searchBox-search-icon">
              <i className="fa fa-search"></i>
            </div>
            <input className="searchBox-input"
                   placeholder={__("Search topic")} />
          </div>
          <div className="topicList-header-middle">
            <div className="topicList-header-middle-label">
              {__("TOPICS")}
            </div>
            <a href="" className="topicList-header-middle-add">
              <i className="fa fa-plus-circle"></i> {__("New Message")}
            </a>
          </div>
        </div>
        <ul>
          <InfiniteScroll
            loadingMore={ fetching_topics }
            loadMore={ this.fetchMoreTopics.bind(this) }
            items={ render_topics }
            elementIsScrollable={ false }
            loader=""
          />
        </ul>
        { fetching_topics && <Loading />}
      </div>
    )
  }
}
