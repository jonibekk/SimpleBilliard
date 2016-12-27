import React from "react";
import {KeyResult} from "~/common/constants/Model";

export default class ValueStartEndInput extends React.Component {
  constructor(props) {
    super(props);
  }

  onChange(e) {
    // intput type="number"ではmaxLengthが無視されるため、
    // ここで入力文字数を制限する
    const length = String(e.target.value).length
    if (length > KeyResult.MAX_LENGTH_VALUE) {
      return false;
    }

    this.props.onChange(e)
  }

  render() {
    const {inputData, kr} = this.props
    // 単位無しだったらエリア非表示
    if (inputData.value_unit == KeyResult.ValueUnit.NONE) {
      return null
    }

    return (
      <div className="goals-create-layout-flex mod-child">
        <input name="start_value" value={inputData.start_value}
               className="form-control goals-create-input-form goals-create-input-form-tkr-range"
               placeholder={0}
               type="text"
               onChange={this.onChange.bind(this)}
               maxLength={KeyResult.MAX_LENGTH_VALUE}
               disabled={kr.value_unit == inputData.value_unit}
        />
        <span className="goals-create-input-form-tkr-range-symbol">
          <i className="fa fa-long-arrow-right" aria-hidden="true"></i>
        </span>
        <input name="target_value" value={inputData.target_value}
               className="form-control goals-create-input-form goals-create-input-form-tkr-range"
               placeholder={100}
               type="text"
               onChange={this.onChange.bind(this)}
               maxLength={KeyResult.MAX_LENGTH_VALUE}/>
      </div>
    )

  }
}
ValueStartEndInput.propTypes = {
  inputData: React.PropTypes.object.isRequired,
  kr: React.PropTypes.object.isRequired,
};
ValueStartEndInput.defaultProps = {
  kr: {}
};
