import React from "react";

export default class UnitSelect extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      opened_unit_list: false
    }
  }

  onChange(e) {
    this.props.onChange(e)
    this.setState({ opened_unit_list: false })
  }

  render() {
    const {value, units} = this.props
    if (units.length == 0) {
      return null
    }

    const unitOptions = units.map((v) => {
      return <option key={v.id} value={v.id}>{this.state.opened_unit_list ? `${v.label}(${v.unit})` : v.unit}</option>
    })

    return(
      <select name="value_unit" value={value}
              className="form-control goals-create-input-form mod-select-units"
              onChange={this.onChange.bind(this)}
              onFocus={ () => { this.setState({ opened_unit_list: true })} }
              onBlur={ () => { this.setState({ opened_unit_list: false })} }>
        {unitOptions}
      </select>
    )

  }
}
UnitSelect.propTypes = {
  units: React.PropTypes.array.isRequired
};
