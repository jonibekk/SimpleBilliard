import React from "react";
import {KeyResult} from "~/common/constants/Model";

export default class ValueStartEndInput extends React.Component {
  constructor(props) {
    super(props);
  }

  onChange(e) {
    this.props.onChange(e)
  }

  render() {
    const {inputData} = this.props
    // 単位無しだったらエリア非表示
    if (inputData.value_unit == KeyResult.ValueUnit.NONE) {
      return null
    }

    return (
      <div>
        <div className="goals-create-layout-flex">
          <input name="start_value" value={inputData.start_value}
                 className="form-control goals-create-input-form goals-create-input-form-tkr-range" type="text"
                 placeholder={0} onChange={this.onChange.bind(this)}/>
          <span className="goals-create-input-form-tkr-range-symbol">&gt;</span>
          <input name="target_value" value={inputData.target_value}
                 className="form-control goals-create-input-form goals-create-input-form-tkr-range" type="text"
                 placeholder={100} onChange={this.onChange.bind(this)}/>
        </div>
      </div>
    )

  }
}
ValueStartEndInput.propTypes = {
  inputData: React.PropTypes.object.isRequired,
};

