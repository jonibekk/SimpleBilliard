import React from "react";
import {browserHistory, Link} from "react-router";
import Base from "~/common/components/Base";
import {isMobileApp, disableAsyncEvents} from "~/util/base";
import InfiniteScroll from "redux-infinite-scroll";
import Message from "./elements/search_messages/Message";

export default class SearchMessages extends Base {
  constructor(props) {
    super(props);
  }

  componentWillMount() {
    // Set resource ID included in url.
    const topic_id = this.props.params.topic_id;
    this.props.setResourceId(topic_id);
    this.props.setUaInfo();
    this.props.initLayout();
    this.props.fetchInitialData(this.props.params.topic_id);
  }

  componentDidMount() {
    super.componentDidMount.apply(this);
    disableAsyncEvents()

    const topic_id = this.props.params.topic_id;
    let {pusher_info} = this.props.detail;
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this);
  }

  render() {
    const {topic, messages} = this.props.search_messages;
    const render_messages = messages.map((topic, index) => {
      return (
        <Message topic={ topic }
               key={ topic.id }
               type={ search_conditions.type }
               index={ index }/>
      )
    })
    const sp_class = this.props.is_mobile_app ? "mod-sp" : "";
    const header_styles = {
      top: this.props.mobile_app_layout.header_top
    };

    return (
      <div className={`topicSearchMessages ${isMobileApp() ? "" : "panel panel-default"}`}>
        <div
          className={`topicSearchMessages-header ${sp_class}`}
          style={header_styles}
        >
          <div className="topicSearchMessages-header-left">
            <Link to="/topics" className>
              <i className="fa fa-chevron-left topicSearchMessages-header-icon"/>
            </Link>
          </div>
          <div className="topicSearchMessages-header-center">
            <a href="#"
               className="topicSearchMessages-header-center-link">
              <div className="topicSearchMessages-header-title oneline-ellipsis"><span>{topic.display_title}</span></div>
              <div className="topicSearchMessages-header-membersCnt">
                ({topic.members_count})
              </div>
            </a>
          </div>
        </div>
        <ul>
          <InfiniteScroll
            loadingMore={ fetching }
            loadMore={ this.fetchMore }
            items={ render_messages }
            elementIsScrollable={ false }
            loader={ <Loading /> }
          />
        </ul>
      </div>
    )
  }
}
