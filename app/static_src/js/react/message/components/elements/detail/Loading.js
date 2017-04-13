import React from "react";

// TODO:componentize ui parts
export default class Loading extends React.Component {
  render() {
    const {size} = this.props
    return (
      <div className="loadingImg">
        <img src="/img/lightbox/loading.gif" width={size} height={size}/>
      </div>
    )
  }
}

Loading.propTypes = {
  topic: React.PropTypes.number
};

Loading.defaultProps = {
  size: 16
};
