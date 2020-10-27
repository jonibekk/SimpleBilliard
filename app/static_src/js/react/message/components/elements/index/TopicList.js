import React from "react";
import Topic from "./Topic";
import InfiniteScroll from "redux-infinite-scroll";
import Loading from "~/message/components/elements/index/Loading";
import {Link} from "react-router";
import queryString from "query-string";

export default class TopicList extends React.Component {
  constructor(props) {
    super(props)

    this.onFocusSearchBox = this.onFocusSearchBox.bind(this);
    this.fetchMore = this.fetchMore.bind(this);
  }

  componentWillMount() {
    this.fetchInit();
  }

  fetchInit() {
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
    const render_topics = topics.map((topic, index) => {
      return (
        <Topic topic={ topic }
               key={ topic.id }
               index={ index }/>
      )
    })

    return (
      <div className={`topicList ${!is_mobile_app && "panel panel-default"}`}>
        <div className="topicList-header">
          <div className="topicList-header-top">
            <div className="searchBox">
              <div className="searchBox-search-icon">
                <i className="fa fa-search"></i>
              </div>
              <input className="searchBox-input"
                     placeholder={`${__("member")}, ${__("topic")}, ${__("message")}`}
                     onFocus={ this.onFocusSearchBox }/>
            </div>
            <Link to="/topics/create" className="topicList-header-top-add">
              <i className="fa fa-plus"></i> {__("Create")}

            </Link>
          </div>
          <div className="topicList-header-middle">
            <div className="topicList-header-middle-label">
              {__("Topics")}
            </div>
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
