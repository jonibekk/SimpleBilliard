import React from "react";

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

    // HACK: constにしないとeslintにおこられる。
    //       たぶんもっとスマートに実装できる。
    const short_unit_list = []
    const unit_options = units.map((v) => {
      short_unit_list[v.id] = v.unit
      return <option key={v.id} value={v.id}>{ `${v.label}(${v.unit})` }</option>
    })

    return(
      <div className="relative">
          <div className="goals-create-input-form-unit-box">
              <select
                name="value_unit"
                value={value}
                className="form-control goals-create-input-form mod-select-units"
                onChange={this.onChange.bind(this)}
              >
                { unit_options }
              </select>
          </div>
          <span className="goals-create-input-form-unit-label">{ short_unit_list[value] }</span>
      </div>
    )
  }
}
UnitSelect.propTypes = {
  units: React.PropTypes.array.isRequired,
};
