import {connect} from "react-redux";
import Goals from "~/saved_item/components/SavedItems";
import * as actions from "~/saved_item/actions/index";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return {
    setUaInfo: () => dispatch(actions.setUaInfo()),
    fetchInitialData: () => dispatch(actions.fetchInitialData()),
    updateFilter: (type) => dispatch(actions.updateFilter(type)),
    unsave: (item) => dispatch(actions.unsave(item)),
    fetchMore: (url) => dispatch(actions.fetchMore(url))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Goals)

