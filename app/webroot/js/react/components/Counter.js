import React, { Component } from 'react';

export default class Counter extends Component {
  render() {
    const { value, onIncrement, onDecrement } = this.props;
    return (
      <div>
        <h3>Counter: {value}</h3>
        <button onClick={onIncrement}>
          +
        </button>
        {' '}
        <button onClick={onDecrement}>
          -
        </button>
      </div>
    );
  }
}
