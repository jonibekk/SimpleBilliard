import React from 'react';
import {Provider} from 'react-redux';

function mapStateToPropsContainer (state) {
  return {
    count: state.count
  };
}

function mapDispatchToPropsContainer (dispatch) {
  return {
    onClickPlus: () => dispatch(ACTION_INCREMENT_COUNTER),
    onClickMinus: () => dispatch(ACTION_DECREMENT_COUNTER)
  };
}

let App = connect(
  mapStateToPropsContainer,
  mapDispatchToPropsContainer
)(CounterComponent);
