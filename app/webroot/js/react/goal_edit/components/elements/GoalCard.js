import React from "react";
import * as ValueUnit from "../../../common/constants/ValueUnit";
import {nl2br} from "../../../util/element";

export class GoalCard extends React.Component {
  render() {
    const {inputData} = this.props
    if (Object.keys(inputData).length == 0) {
      return null
    }

    let categoryElement = null;
    for (const i in this.props.categories) {
      if (inputData.goal_category_id == this.props.categories[i].id) {
        categoryElement = <p><i className="fa fa-folder-o" aria-hidden="true"></i> {this.props.categories[i].name }</p>
      }
    }

    // 達成開始・目標値表示
    const unitLabel = (key_result, units) => {
      // 完了/未完了の場合
      if (key_result.value_unit == ValueUnit.NONE) {
        for (const v of units) {
          if (v.id == key_result.value_unit) {
            return <li>{v.label}</li>
          }
        }
      }

      // 単位が存在する場合
      for (const v of units) {
        if (v.id == key_result.value_unit) {
          return <li>{key_result.start_value} {v.unit} -> {key_result.target_value} {v.unit}</li>
        }
      }
    }

    const imgUrl = inputData.photo ? inputData.photo.result : this.props.goal.medium_large_img_url;

    return (
      <div className="goals-approval-detail-goal mod-bgglay">
        <div className="goals-approval-detail-table">
          <img className="goals-approval-detail-image" src={ imgUrl } alt="" width="32" height="32"/>
          <div className="goals-approval-detail-info">
            {categoryElement}
            <p>{ inputData.name }</p>
            <div className="goals-approval-detail-tkr">
              <h2 className="goals-approval-detail-tkrtitle"><i className="fa fa-key" aria-hidden="true"></i> Top key
                result</h2>
              <ul className="goals-approval-detail-tkrlist">
                <li>{ inputData.key_result.name }</li>
                {unitLabel(inputData.key_result, this.props.units)}
                <li>{ nl2br(inputData.key_result.description) }</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

GoalCard.propTypes = {
  goal: React.PropTypes.object.isRequired,
  inputData: React.PropTypes.object.isRequired,
  categories: React.PropTypes.array,
  units: React.PropTypes.array.isRequired,
}
