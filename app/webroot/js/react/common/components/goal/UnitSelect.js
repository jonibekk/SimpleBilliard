import React from "react";
import {KeyResult} from "~/common/constants/Model";

export default class UnitSelect extends React.Component {
  constructor(props) {
    super(props);
  }

  onChange(e) {
    this.props.onChange(e)
  }

  render() {
    const {value, units} = this.props
    if (units.length == 0) {
      return null
    }

    const unitOptions = units.map((v) => {
      return <option key={v.id} value={v.id}>{v.label}({v.unit})</option>
    })

  return(
    <select name="value_unit" value={value}
            className="form-control goals-create-input-form goals-create-input-form-tkr-range-unit mod-select"
            onChange={this.onChange.bind(this)}>
      {unitOptions}
    </select>
  )

}
}
UnitSelect.propTypes = {
  value: React.PropTypes.string,
  units: React.PropTypes.array.isRequired,
};

