import React from "react";
import ReactDOM from "react-dom";
import InfiniteScroll from "redux-infinite-scroll";
import Loading from "~/message/components/elements/index/Loading";
import Topic from "./Topic";
import {SearchType} from "~/message/constants/Statuses";

export default class TopicSearchList extends React.Component {
  constructor(props) {
    super(props);
    this.changeSearchType = this.changeSearchType.bind(this);
  }

  componentDidMount() {
    this.focusSearchBox()

    // set keyword in previous searching
    const keyword = this.props.data.search_conditions.keyword
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

  changeSearchType(e, val) {
    e.preventDefault()
    this.focusSearchBox();
    this.props.changeSearchType(val);
  }


  render() {
    const {search_result, fetching, search_conditions, is_mobile_app, search_total_count, changed_search_conditions} = this.props.data
    const render_topics = search_result.map((data, index) => {
      return (
        <Topic data={ data }
               key={ data.topic.id }
               type={ search_conditions.type }
               keyword={ search_conditions.keyword }
               index={ index }/>
      )
    })

    const search_tabs = {
      "topics": __("Topics"),
      "messages" : __("Messages"),
    }
    let search_tabs_el = [];

    const search_tabs_keys = Object.keys(search_tabs)
    for (let i = 0; i < search_tabs_keys.length; i++) {
      const key = search_tabs_keys[i];

      let activeClass = '';
      if ((!search_conditions.type && i == 0) || search_conditions.type == key) {
        activeClass = 'mod-active';
      }
      search_tabs_el.push(
        <li key={key} className={`topicSearchList-searchCategoryTabs-item ${activeClass}`}>
          <a href="#"
             onClick={(e) => this.changeSearchType(e, key)}>
            {search_tabs[key]}
          </a>
        </li>
      )
    }

    return (
      <div className={`topicSearchList ${!is_mobile_app && "panel panel-default"}`}>
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
                     placeholder={`${__("member")}, ${__("topic")}, ${__("message")}`}
                     onChange={ this.inputKeyword.bind(this) }
                     ref="search"/>
            </div>
          </div>
          <div className="topicSearchList-header-cancel">
            <a className="topicSearchList-header-cancel-button"
               onClick={ this.props.cancelSearchMode.bind(this) }>{__("Cancel")}</a>
          </div>
        </div>
        <div className="topicSearchList-searchCategoryTabs-wrapper">
          <ul className="topicSearchList-searchCategoryTabs">
            {search_tabs_el}
          </ul>
        </div>

        {
          (() => {
            if (changed_search_conditions) {
              return (
                <Loading />
              )
            } else if (!search_conditions.keyword || (!fetching && search_result.length == 0)) {
              // not results
              return (
                <div className="topicSearchList-notFound">
                  { __("No results found") }
                </div>
              )
            } else {
              const search_total_count_el = search_conditions.type === SearchType.TOPICS ? <p className="topicSearchList-hitCount">{sprintf(__("Search result %d topics hit"), search_total_count)}</p> : "";

              // search results
              return (
                <div>
                  {search_total_count_el}
                  <ul className={search_conditions.type === SearchType.TOPICS ? '' : 'pt_10px'}>
                    <InfiniteScroll
                      loadingMore={ fetching }
                      loadMore={ this.fetchMoreSearch.bind(this) }
                      items={ render_topics }
                      elementIsScrollable={ false }
                      loader={ <Loading /> }
                    />
                  </ul>
                </div>
              )
            }
          })()
        }
      </div>
    )
  }
}

TopicSearchList.defaultProps = {
  data: {
    search_result: [],
    fetching: false
  }
}
