import React from "react";
import TopicList from "./elements/index/TopicList";
import TopicSearchList from "./elements/search/TopicSearchList";
import {disableAsyncEvents} from "~/util/base";
import queryString from "query-string";

export default class Index extends React.Component {
  constructor(props) {
    super(props);
  }

  componentWillMount() {
    this.props.setUaInfo();
    if (!this.props.index.init_completed) {
      const search_conditions = queryString.parse(location.search);
      if (search_conditions.keyword || search_conditions.type) {
        this.props.changeToSearchMode();
        this.props.searchData(search_conditions);
        return;
      }
    }
  }

  componentDidMount() {
    disableAsyncEvents();
    this.props.init_completed();
  }

  render() {
    const {is_search_mode, init_completed} = this.props.index;
console.log({is_search_mode, init_completed});
    if (!init_completed) {
      return null;
    }

    return (
      <div>
        {(() => {
          if (!is_search_mode) {
            return (
              <TopicList
                data={ this.props.index }
                fetchInitData={ this.props.fetchInitData }
                fetchMore={ (url) => this.props.fetchMore(url) }
                changeToSearchMode={ this.props.changeToSearchMode }
                searchData={ (search_conditions) => this.props.searchData(search_conditions) }
              />
            )
          } else {
            return (
              <TopicSearchList
                data={ this.props.search }
                fetchMoreSearch={ (url) => this.props.fetchMoreSearch(url) }
                inputKeyword={ (keyword) => this.props.inputKeyword(keyword) }
                cancelSearchMode={ this.props.cancelSearchMode }
                emptyTopics={ this.props.emptyTopics }
                changeSearchType={ (type) => this.props.changeSearchType(type) }
              />
            )
          }
        })()}
      </div>
    )
  }
}
