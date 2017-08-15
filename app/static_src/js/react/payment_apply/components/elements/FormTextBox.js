import React from "react";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";
import {connect} from "react-redux";
import {updateInputData} from "~/payment_apply/actions/index";

class FormTextBox extends React.Component {
  constructor(props) {
    super(props);
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
        <InvalidMessageBox message={this.props.err_msg}/>
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
  err_msg: React.PropTypes.string,
  max_length: React.PropTypes.number,
};
FormTextBox.defaultProps = {
  id: "",
  name: "",
  type: "text",
  label: "",
  placeholder: "",
  err_msg: "",
  max_length: 255
};
export default connect()(FormTextBox);
