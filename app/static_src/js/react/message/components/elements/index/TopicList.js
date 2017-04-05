import React from "react";
import Topic from "./Topic";
import InfiniteScroll from "redux-infinite-scroll";
import Loading from "~/message/components/elements/index/Loading";
import {Link} from "react-router";

export default class TopicList extends React.Component {
  constructor(props) {
    super(props)

    this.onFocusSearchBox = this.onFocusSearchBox.bind(this)
    this.fetchMore = this.fetchMore.bind(this)
  }

  componentWillMount() {
    if (this.props.data.topics.length == 0) {
      this.props.fetchInitData()
    }
  }

  fetchMore() {
    const next_url = this.props.data.next_url
    if (!next_url) {
      return
    }

    this.props.fetchMore(next_url)
  }

  onFocusSearchBox() {
    this.props.changeToSearchMode()
  }

  render() {
    const {topics, fetching, is_mobile_app} = this.props.data
    const render_topics = topics.map((topic) => {
      return (
        <Topic topic={ topic }
               key={ topic.id }/>
      )
    })

    return (
      <div className={`topicList ${!is_mobile_app && "panel panel-default"}`}>
        <div className="topicList-header">
          <div className="searchBox">
            <div className="searchBox-search-icon">
              <i className="fa fa-search"></i>
            </div>
            <input className="searchBox-input"
                   placeholder={__("Search topic")}
                   onFocus={ this.onFocusSearchBox }/>
          </div>
          <div className="topicList-header-middle">
            <div className="topicList-header-middle-label">
              {__("TOPICS")}
            </div>
            <Link to="/topics/create" className="topicList-header-middle-add">
              <i className="fa fa-plus-circle"></i> {__("New Message")}
            </Link>
          </div>
        </div>
        <ul>
          <InfiniteScroll
            loadingMore={ fetching }
            loadMore={ this.fetchMore }
            items={ render_topics }
            elementIsScrollable={ false }
            loader={ <Loading /> }
          />
        </ul>
      </div>
    )
  }
}
