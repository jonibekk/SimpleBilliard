import { connect } from 'react-redux'
import CompleteComponent from '../components/Complete'
import * as actions from "../actions/index";
import * as common from "./common";

function mapStateToProps(state) {
  return state
}

function mapDispatchToProps(dispatch) {
  return Object.assign({}, common.getCommonDispatchToProps(dispatch), {
  });
}

export default connect(mapStateToProps, mapDispatchToProps)(CompleteComponent)
