import React from "react";
import SavedItem from "~/saved_item/components/elements/SavedItem";
import NoSavedItems from "~/saved_item/components/elements/NoSavedItems";
import NotFoundItem from "~/saved_item/components/elements/NotFoundItem";
import Loading from "~/saved_item/components/elements/Loading";
import InfiniteScroll from "redux-infinite-scroll";
import {Post} from "~/common/constants/Model";

export default class SavedItems extends React.Component {
  constructor(props) {
    super(props)
    this.updateFilter = this.updateFilter.bind(this);
  }

  componentWillMount() {
    this.props.setUaInfo();
    this.props.fetchInitialData()
  }

  componentWillReceiveProps(nextProps) {
  }

  showFilter() {
    this.props.updateData({show_filter: !this.props.saved_item.show_filter})
  }

  updateFilter(e, type) {
    e.preventDefault()
    this.props.updateFilter(type)
  }


  fetchMore() {
    const {search_result} = this.props.saved_item
    const url = search_result.paging.next
    if (!url) {
      return
    }
    this.props.fetchMore(url)

  }

  renderSavedItems(items) {
    return items.map((item) => {
      return (
        <SavedItem saved_item={item} key={`saved-item-${item.id}`}/>
      )
    })
  }

  render() {
    const props = this.props.saved_item

    const {data, paging, counts} = props.search_result
    const def_filter_label = `${__("All")} (${counts.all})`;
    const filters = [
      {label: def_filter_label, type: "", ico: "fa-list"},
      {label: `${__("Actions")} (${counts.action})`, type: Post.TYPE.ACTION, ico: "fa-check-circle"},
      {label: `${__("Posts")} (${counts.normal})`, type: Post.TYPE.NORMAL, ico: "fa-comment-o"},
    ];

    let filter_label = "";
    if (!props.search_conditions.type) {
      filter_label = def_filter_label;
    } else {
      for (let i = 0; i < filters.length; i++) {
        if (props.search_conditions.type == filters[i].type) {
          filter_label = filters[i].label
          break;
        }
      }
    }

    let search_result_el;
    if (props.loading == true) {
      search_result_el = <Loading/>
    } else if (data.length > 0) {
      search_result_el = (
        <ul className="bd-b-sc4">
          <InfiniteScroll
            loadMore={this.fetchMore.bind(this)}
            loader={<Loading/>}
            loadingMore={props.loading_more}
            items={this.renderSavedItems(data)}
            elementIsScrollable={false}
          />
        </ul>
      )
    } else {
      if (props.search_conditions.type) {
        search_result_el = <NotFoundItem/>
      } else {
        search_result_el = <NoSavedItems/>
      }
    }

    return (
      <section className={`savedItemList ${!props.is_mobile_app && "panel panel-default"}`}>
        <header className="savedItemList-header">
          <div className="savedItemList-header-left">
            <h1 className="savedItemList-title">{__("SAVED ITEMS")}</h1>
          </div>
          {counts.all > 0 &&
          <div className="savedItemList-header-right">
            <div role="group">
              <button className="dropdown-toggle mod-noAppearance" data-toggle="dropdown" role="button"
                 aria-expanded="false"><span className="true">{filter_label}</span><i
                className="fa fa-angle-down ml_2px"></i></button>
              <ul className="dropdown-menu pull-right" role="menu">
                {(() => {
                  let filters_el = []
                  for (let i = 0; i < filters.length; i++) {
                    const filter = filters[i]
                    filters_el.push(
                      <li key={`saved-items-filter-${i}`}>
                        <a href="#"
                           onClick={(e) => this.updateFilter(e, filter.type)}>
                          <i className={`fa ${filter.ico} mr_8px`}></i>
                          {filter.label}
                        </a>
                      </li>
                    )
                  }
                  return filters_el
                })()}
              </ul>
            </div>
          </div>
          }
        </header>

        {search_result_el}
      </section>
    )
  }
}
