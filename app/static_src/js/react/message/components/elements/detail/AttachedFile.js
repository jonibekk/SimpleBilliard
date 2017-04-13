import React from "react";
import * as Model from "~/common/constants/Model";
import FileTypeIcon from "~/message/components/elements/ui_parts/FileTypeIcon";

// TODO:Refactoring markup after we can upload file and save message.
export default class AttachedFile extends React.Component {
  constructor(props) {
    super(props);
  }

  getImgDimensions(img) {
    // TODO: 本来は画像を表示する親要素(<div class="topicDetail-messages-item-right">)の幅によって計算した方が良いが暫定的に対応
    const maxWidth = 200;
    if (img.thumbnail_width <= maxWidth) {
      return {
        width: img.thumbnail_width,
        height: img.thumbnail_height,
      }
    }
    const height = img.thumbnail_height * (maxWidth / img.thumbnail_width);
    return {
      width: maxWidth,
      height
    }
  }

  render() {
    const {attached_file, message_id} = this.props

    switch (parseInt(attached_file.file_type)) {
      case Model.AttachedFile.FileType.IMG:
        const dimensions = this.getImgDimensions(attached_file);

        return (
          <div className="mb_12px">
            <a href={ attached_file.preview_url }
               rel='lightbox'
               data-lightbox={`MessageLightBox_${message_id}`}>
              <img
                className="lazy"
                src={ attached_file.thumbnail_url }
                width={dimensions.width}
                height={dimensions.height}
              />
            </a>
          </div>
        )
      case Model.AttachedFile.FileType.VIDEO:
      case Model.AttachedFile.FileType.DOC:

        return (
          <div className="topicDetail-messages-item-attachedFiles-item">
            <div className="col col-xxs-1 messanger-attached-files-icon">
              <a href={attached_file.preview_url} target="_blank">
                <div>
                  <FileTypeIcon file_ext={attached_file.file_ext} />
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
                      <i className="fa fa-external-link-square mr_4px"></i>{__("Preview")}
                    </div>
                  </div>
                </a>
                <a className="link-dark-gray" href={attached_file.download_url} target="_blank">
                  <div className="col col-xxs-6 text-center file-btn-wap">
                    <div className="file-btn">
                      <i className="fa fa-download mr_4px"></i>{__("Download")}
                    </div>
                  </div>
                </a>
              </div>

            </div>
          </div>
        )
      default:
        return null;
    }
  }
}
AttachedFile.propTypes = {
  attached_file: React.PropTypes.object,
  message_id: React.PropTypes.string
};

AttachedFile.defaultProps = {
  attached_file: {},
  message_id: "0"
};
