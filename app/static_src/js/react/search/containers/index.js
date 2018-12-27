import {connect} from "react-redux";
import Search from "~/search/components/Search";
import * as actions from "~/search/actions/index";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    fetchInitialData: () => dispatch(actions.fetchInitialData()),
    updateData: (data, key = "") => dispatch(actions.updateData(data, key = "")),
    updateFilter: (data) => dispatch(actions.updateFilter(data)),
    updateKeyword: (data) => dispatch(actions.updateKeyword(data)),
    fetchMoreResults: (url) => dispatch(actions.fetchMore(url))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Search)

