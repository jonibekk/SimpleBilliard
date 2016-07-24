import { connect } from 'react-redux'
import { selectTerm, selectStartMonth, selectTimezone, changeToTimezoneSelectMode } from '../actions/term_actions'
import TermComponent from '../components/term'

function mapStateToProps(state) {
  return { term: state.term }
}

function mapDispatchToProps(dispatch) {
  return {
    selectTerm: term => dispatch(selectTerm(term)),
    selectStartMonth: start_month => dispatch(selectStartMonth(start_month)),
    selectTimezone: timezone => dispatch(selectTimezone(timezone)),
    changeToTimezoneSelectMode: () => dispatch(changeToTimezoneSelectMode())
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(TermComponent)
