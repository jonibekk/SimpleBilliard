import React from "react";
import ReactDom from "react-dom";
import {connect} from "react-redux";
import * as actions from "~/message/actions/detail";
import UploadDropZone from "~/message/components/elements/detail/UploadDropZone";
import UploadPreview from "~/message/components/elements/detail/UploadPreview";


class Footer extends React.Component {
  constructor(props) {
    super(props)
    // HACK:Display drop zone when dragging
    // reference:http://qiita.com/sounisi5011/items/dc4878d3e8b38101cf0b
    this.state = {
      is_drag_over: false,
      is_drag_start: false,
    }
  }

  sendLike(e) {
    this.props.dispatch(
      actions.sendLike()
    );
  }

  sendMessage(e) {
    this.props.dispatch(
      actions.sendMessage()
    );
  }

  onChangeMessage(e) {
    this.props.dispatch(
      actions.onChangeMessage(e.target.value)
    );
  }

  uploadFiles(files) {
    if (!files) {
      return;
    }

    this.props.dispatch(
      actions.uploadFiles(files)
    );
  }

  dragEnter(e) {
    e.stopPropagation();
    e.preventDefault();
    this.setState({is_drag_start: true});
  }

  dragOver(e) {
    this.setState({is_drag_over: true});
    e.stopPropagation();
    e.preventDefault();
  }

  dragLeave(e) {
    if (this.state.is_drag_start) {
      this.setState({is_drag_start: false});
    } else {
      this.setState({is_drag_over: false});
    }
  }

  drop(e) {
    e.stopPropagation();
    e.preventDefault();

    this.setState({is_drag_over: false});

    const files = e.dataTransfer.files;

    this.uploadFiles(files);
  }

  selectFile(e) {
    ReactDom.findDOMNode(this.refs.file).click();
  }

  changeFile(e) {
    const files = e.target.files;
    this.uploadFiles(files);
  }

  render() {
    return (
      <div className={`topicDetail-footer ${this.state.is_drag_over && "uploadFileForm-wrapper"}`}
           onDrop={this.drop.bind(this)}
           onDragEnter={this.dragEnter.bind(this)}
           onDragOver={this.dragOver.bind(this)}
           onDragLeave={this.dragLeave.bind(this)}
      >
        {this.state.is_drag_over && <UploadDropZone/>}
        <UploadPreview files={this.props.files} />
        <form>
          <div className="topicDetail-footer-box">
            <div className="topicDetail-footer-box-left">
              <button type="button"
                      className="btn btnRadiusOnlyIcon mod-upload"
                      onClick={this.selectFile.bind(this)}
              />
              <input type="file" className="hidden" ref="file" onChange={this.changeFile.bind(this)}/>
            </div>
            <div className="topicDetail-footer-box-center">
                <textarea
                  className="form-control disable-change-warning"
                  rows={1} cols={30} placeholder={__("Reply")}
                  name="message_body" defaultValue=""
                  onChange={this.onChangeMessage.bind(this)}
                />
              {this.props.err_msg &&
              <div className="has-error">
                    <span className="has-error help-block">
                      {this.props.err_msg}
                    </span>
              </div>
              }
            </div>
            <div className="topicDetail-footer-box-right">
              {(() => {
                if (this.props.message || this.props.file_ids.length > 0) {
                  return (
                    <button type="button"
                            className="btn btnRadiusOnlyIcon mod-send"
                            onClick={this.sendMessage.bind(this)}
                            disabled={this.props.is_saving && "disabled"}/>
                  )
                } else {
                  return (
                    <button type="button"
                            className="btn btnRadiusOnlyIcon mod-like"
                            onClick={this.sendLike.bind(this)}
                            disabled={(this.props.is_saving || this.props.is_uploading) && "disabled"}/>
                  )
                }
              })(this)}
            </div>
          </div>
        </form>
      </div>
    )
  }
}

Footer.propTypes = {
  message: React.PropTypes.string,
  file_ids: React.PropTypes.array,
  files: React.PropTypes.array,
  is_saving: React.PropTypes.bool,
  is_uploading: React.PropTypes.bool,
  err_msg: React.PropTypes.string,
};
Footer.defaultProps = {
  message: "",
  file_ids: [],
  files: [],
  is_saving: false,
  is_uploading: false,
  err_msg: "",
};
export default connect()(Footer);
