import { connect } from 'react-redux'
import * as actions from '~/message/actions/detail'
import DetailComponent from '~/message/components/Detail'

function mapStateToProps(state) {
  return { detail: state.detail}
}

function mapDispatchToProps(dispatch) {
  return {
    fetchInitialData: (topic_id) => dispatch(actions.fetchInitialData(topic_id)),
    fetchMoreGoals: (url) => dispatch(actions.fetchMoreGoals(url))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(DetailComponent)
