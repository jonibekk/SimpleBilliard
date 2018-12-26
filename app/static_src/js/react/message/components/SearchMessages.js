import React from "react";
import {browserHistory, Link} from "react-router";
import Base from "~/common/components/Base";
import {isMobileApp, disableAsyncEvents} from "~/util/base";
import InfiniteScroll from "redux-infinite-scroll";
import Message from "./elements/search_messages/Message";
import queryString from "query-string";
import Loading from "~/message/components/elements/index/Loading";

export default class SearchMessages extends Base {
  constructor(props) {
    super(props);
    this.setState({current_url: encodeURI(location.href)});
  }

  componentWillMount() {
    // Set resource ID included in url.
    const topic_id = this.props.params.topic_id;
    this.props.setResourceId(topic_id);
    this.props.setUaInfo();
    this.props.initLayout();
    const query_params = queryString.parse(location.search);
    this.props.fetchInitialData(this.props.params.topic_id, query_params);

  }

  componentDidMount() {
    super.componentDidMount.apply(this);
    disableAsyncEvents()
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this);
  }

  fetchMore() {
    const {next_url} = this.props.search_messages
    if (!next_url) {
      return
    }
    this.props.fetchMore(next_url)
  }


  render() {
    console.log({props: this.props});
    // const {topic_id, search_total_count, fetching, is_mobile_app, mobile_app_layout, topic, messages} = this.props.search_messages;
    const props = this.props.search_messages;
    console.log('SearchMessages: render');
    const render_messages = props.messages.map((item, index) => {
      return (
        <Message
          item={ item }
          key={ item.id }
          index={ index }
          />
      )
    })
    const sp_class = props.is_mobile_app ? "mod-sp" : "";
    const header_styles = {
      top: props.mobile_app_layout.header_top
    };

    return (
      <div className={`topicSearchMessages ${isMobileApp() ? "" : "panel panel-default"}`}>
        <div
          className={`topicSearchMessages-header ${sp_class}`}
          style={header_styles}
        >
          <div className="topicSearchMessages-header-left">
            <Link to={`/topics?keyword=${props.search_conditions.keyword}&type=messages`} className>
              <i className="fa fa-chevron-left topicSearchMessages-header-icon"/>
            </Link>
          </div>
          <div className="topicSearchMessages-header-center">
            <a href="#"
               className="topicSearchMessages-header-center-link">
              <div className="topicSearchMessages-header-title oneline-ellipsis"><span>{props.topic.display_title}</span></div>
              <div className="topicSearchMessages-header-membersCnt">
                ({props.topic.members_count})
              </div>
            </a>
          </div>
        </div>
        <p className="topicSearchList-hitCount">{__("Search result %d Topics hit", props.search_total_count)}</p>
        <ul>
          <InfiniteScroll
            loadingMore={ props.fetching }
            loadMore={ this.fetchMore.bind(this) }
            items={ render_messages }
            elementIsScrollable={ false }
            loader={ <Loading /> }
          />
        </ul>
      </div>
    )
  }
}
