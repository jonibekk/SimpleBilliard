import { connect } from 'react-redux'
import * as actions from '../actions/term_actions'
import TermComponent from '../components/term'

function mapStateToProps(state) {
  return { term: state.term, validate: state.validate }
}

function mapDispatchToProps(dispatch) {
  return {
    changeToTimezoneSelectMode: () => dispatch(actions.changeToTimezoneSelectMode()),
    postTerms: terms => dispatch(actions.postTerms(terms)),
    setStartMonthList: selected_term => dispatch(actions.setStartMonthList(selected_term)),
    dispatch: action => dispatch(action)
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(TermComponent)
