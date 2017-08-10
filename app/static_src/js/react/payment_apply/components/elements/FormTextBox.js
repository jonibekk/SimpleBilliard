import React from "react";
import {connect} from "react-redux";
import {updateInputData} from "~/payment_apply/actions/index";

class FormTextBox extends React.Component {
  constructor(props) {
    super(props);
  }

  onChange(e) {
    let data = {}
    data[e.target.name] = e.target.value;
    this.props.dispatch(updateInputData(data, 'payment_setting'))
  }

  render() {
    return (
      <div className="form-group">
        <label htmlFor={this.props.id} className="circle-create-label">
          {this.props.label}
        </label>
        <input
          id={this.props.id}
          type={this.props.type}
          name={this.props.name}
          className="form-control"
          placeholder={this.props.placeholder}
          maxLength={this.props.max_length}
          onChange={(e) => this.props.onChange(e)}
        />
      </div>
    )

  }
}
FormTextBox.propTypes = {
  id: React.PropTypes.string,
  name: React.PropTypes.string,
  type: React.PropTypes.string,
  label: React.PropTypes.string,
  placeholder: React.PropTypes.string,
  max_length: React.PropTypes.number,
  onChange:React.PropTypes.func,
};
FormTextBox.defaultProps = {
  id: "",
  name:"",
  type: "text",
  label: "",
  placeholder:"",
  max_length: 255
};
export default connect()(FormTextBox);
