import React from "react";
import {connect} from "react-redux";

class NoItem extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const { keyword } = this.props;
    return (
      <div className="bd-b-sc4 searchPage-noItem">
        {/*<p className="text-align_c">*/}
          {/*「<span className="searchPage-noItem-searchKeyword">{ keyword }</span>」*/}
        {/*</p>*/}
        {/*<p className="text-align_c">*/}
          {/*{__("There is no information found that matches keyword.")}*/}
        {/*</p>*/}
        <p className="text-align_c">{__("Information matching the search could not be found")}</p>
      </div>
    )
  }
}

NoItem.propTypes = {
  keyword: React.PropTypes.string
}

NoItem.defaultProps = {
  keyword: "",
}

export default connect()(NoItem);
