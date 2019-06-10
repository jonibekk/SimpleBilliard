import React from "react";
import ReactDOM from "react-dom";
import * as KeyCode from "~/common/constants/KeyCode";
import SearchItem from "~/search/components/elements/SearchItem";
import NoItem from "~/search/components/elements/NoItem";
import Loading from "~/search/components/elements/Loading";
import InfiniteScroll from "redux-infinite-scroll";

export default class SearchItems extends React.Component {
  constructor(props) {
    super(props)
    this.updateFilter = this.updateFilter.bind(this);
  }

  componentWillMount() {
  }

  componentDidMount() {
    this.focusInput();
  }

  focusInput() {
    ReactDOM.findDOMNode(this.refs.keyword).focus()
  }

  componentWillReceiveProps(nextProps) {
  }

  updateFilter(e, key, val) {
    e.preventDefault()
    this.focusInput();
    this.props.updateFilter({[key]: val})
  }


  searchByKeyword(e) {
    e && e.preventDefault()
    this.props.updateFilter({keyword: ReactDOM.findDOMNode(this.refs.keyword).value.trim()})
  }

  updateKeyword(e) {
    e && e.preventDefault()
    this.props.updateKeyword({keyword: e.target.value})
  }

  fetchMoreResults() {
    const {search_result} = this.props.search
    const type = this.props.search.search_conditions.type
    const cursor = search_result.paging
    if (!cursor) {
      return
    }
    this.props.fetchMoreResults({type, cursor})
  }

  renderSearchItems(items) {
    const type = this.props.search.search_conditions.type
    return items.map((item) => {
      const uid = this.makeRandomStr()
      return (
        <SearchItem item={item} type={type} key={`${uid}`}/>
      )
    })
  }

  makeRandomStr(length = 8) {
    // 生成する文字列に含める文字セット
    const c = "abcdefghijklmnopqrstuvwxyz0123456789";

    const cl = c.length;
    let r = "";
    for (let i = 0; i < length; i++) {
      r += c[Math.floor(Math.random() * cl)];
    }
    return r
  }

  render() {
    const props = this.props.search

    const items = props.search_result.data
    const order = props.search_conditions["order"] ? props.search_conditions["order"] : "new";

    // 検索結果エリア
    let search_result_el;
    let exist_item = false;
    if (props.loading == true) {
      search_result_el = <Loading />
    } else if (props.search_result.count > 0) {
      search_result_el = (
        <InfiniteScroll
          loadMore={this.fetchMoreResults.bind(this)}
          loader={<Loading />}
          loadingMore={props.loading_more}
          items={this.renderSearchItems(items)}
          elementIsScrollable={false}
        />
      )
      exist_item = true
    } else {
      search_result_el = <NoItem keyword={props.search_conditions.keyword} />
    }

    const search_tabs = {
      "circle_post": __("Posts"),
      "action" : __("Actions"),
      "users" : __("Members"),
      "circles" : __("Circles"),
    }
    let search_tabs_el = [];

    const search_tabs_keys = Object.keys(search_tabs)
    for (let i = 0; i < search_tabs_keys.length; i++) {
      const key = search_tabs_keys[i];

      let activeClass = '';
      if ((!props.search_conditions.type && i == 0) || props.search_conditions.type == key) {
        activeClass = 'mod-active';
      }
      search_tabs_el.push(
        <li key={key} className={`searchPage-searchCategoryTabs-item ${activeClass}`}>
          <a href="#"
             onClick={(e) => this.updateFilter(e, "type", key)}>
            {search_tabs[key]}
          </a>
        </li>
      )
    }


    return (
      <div className="panel panel-default searchPage">
        {/* search by keyword */}
        <div className="panel-block">
          <form onSubmit={this.searchByKeyword.bind(this)}>
            <div className="search-keyword mb_10px">
              <input type="text" className="search-keyword-input" placeholder={__("Search (Complete match with ”…”)")}
                     ref="keyword"
                     maxLength="50"
                     value={props.search_conditions.keyword || ""}
                     onChange={(e) => this.updateKeyword(e)}
              />
              <span onClick={this.searchByKeyword.bind(this)} className="search-keyword-submit fa fa-search"/>
            </div>
          </form>
        </div>

        <div className="searchPage-searchCategoryTabs-wrapper">
          <ul className="searchPage-searchCategoryTabs">
            {search_tabs_el}
          </ul>
        </div>
        {/* search result count and order */}
        <div className={`panel-block ${exist_item ? "" : "hide"}`}>
          <div className="row">
            <div className="pull-left">{__("Search result")} {props.search_result.count}{__(" count")}</div>
          </div>
        </div>

        {search_result_el}
      </div>
    )
  }
}
