import React from "react";
import Model from "~/common/constants/Model";

// TODO:Refactoring markup after we can upload file and save message.
export default class AttachedFile extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const {attached_file} = this.props

    switch (attached_file.file_type) {
      case Model.AttachedFile.IMG:
      case Model.AttachedFile.VIDEO:
        return (
          <div>
            <a href={attached_file.preview_url}>
              <img src={attached_file.thumbnail_url}/>
            </a>
          </div>
        )
      case Model.AttachedFile.DOC:
        return (
          <div>
            <div className="col col-xxs-1 messanger-attached-files-icon">
              <a href={attached_file.preview_url} target="_blank">
                <div>
                  <i className="fa fa-file-excel-o file-excel-icon"></i>
                </div>
              </a>
            </div>
            <div className="col col-xxs-10 file-info-wrap">
              <a href={attached_file.preview_url} target="_blank">
                <span className="font_14px font_bold font_verydark">
                  {attached_file.attached_file_name}
                </span>
              </a>
              <div className="font_11px font_lightgray">
                <span className="">{attached_file.file_ext}</span>
              </div>
              <div className="row file-btn-group">
                <a className="link-dark-gray"
                   href={attached_file.preview_url}
                   target="_blank">
                  <div className="col col-xxs-6 text-center file-btn-wap">
                    <div className="file-btn">
                      <i className="fa fa-external-link-square"></i>{__("Preview")}
                    </div>
                  </div>
                </a>
                <a className="link-dark-gray" href={attached_file.download_url}>
                  <div className="col col-xxs-6 text-center file-btn-wap">
                    <div className="file-btn">
                      <i className="fa fa-download"></i>{__("Download")}
                    </div>
                  </div>
                </a>
              </div>

            </div>
          </div>
        )
    }
  }
}
AttachedFile.propTypes = {
  attached_file: React.PropTypes.object,
};

AttachedFile.defaultProps = {
  attached_file: {},
};
