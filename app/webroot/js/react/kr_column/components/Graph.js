import React from "react";
import c3 from "c3";
import "c3/c3.css";

export default class Graph extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      flush: false,
      chart: null
    };
    this.flushChart = this.flushChart.bind(this);
    // this.tooltipContents = this.tooltipContents.bind(this);
  }

  flushChart(e) {
    this.setState({
      flush: true
    });
  }

  renderChart(data) {
    if (data.length == 0) {
      return;
    }
    // 日毎の進捗データ(data[2])は実際は['data',1,2,3...]という最初の要素が名称となる配列の形になっているため、末尾のインデックスはlength-1ではなく-2となる。
    const last_index = data[2].length - 2;
    const chart = this.generateChart(data);
    chart.tooltip.show({mouse:[last_index, 50], index: last_index});
  }

  generateChart (data) {
    if (this.state.chart) {
      return this.state.chart;
    }
    const last_index = data[2].length - 2;
    let chart = c3.generate({
      size: {
        height: 200
      },
      data: {
        columns: data,
        types: {
          "sweet_spot_top": 'area',
          "sweet_spot_bottom": 'area',
          "data": 'line',
        },
        colors: {
          "sweet_spot_top": '#d4f2fc',
          "sweet_spot_bottom": '#fff',
          "data": '#4a98ef',
        },
      },
      point: {
        r: function (d) {
          return (d.index == last_index && d.id == "data") ? 5 : 0;
        },
        focus: {
          expand: {
            enabled: false
          }
        }
      },
      grid: {
        focus: {
          show: false
        }
      },
      legend: {
        show: false
      },
      axis: {
        x: {
          show: false,
        },
        y: {
          show: false
        }
      },
      tooltip: {
        contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
          /* tooltipデザインカスタマイズ */
          const $$ = this;
          const config = $$.config
          const CLASS = $$.CLASS;
          const valueFormat = config.tooltip_format_value || defaultValueFormat;
          const name = __("Current");

          // HTML作成
          let el = `<table class="${CLASS.tooltip}">`;
          for (let i = 0; i < d.length; i++) {
            if (!(d[i] && (d[i].value || d[i].value === 0))) {
              return "";
            }

            if (d[i].id !== 'data') {
              continue;
            }

            const value = valueFormat(d[i].value, d[i].ratio, d[i].id, d[i].index);
            el += `<tr class="${CLASS.tooltipName}-${d[i].id}">
                    <td class="name">${name}</td>
                    <td class="value">${value}%</td>
                    </tr>`;
          }

          return el + "</table>";
        }
      }
    });

    // setStateだと以下エラーが発生するため通常の代入
    // Warning: setState(…): Cannot update during an existing state transition
    this.state.chart = chart;
    return chart;
  }

  componentDidMount() {
    this.renderChart(this.props.progress_graph.data);
  }

  render() {
    const {progress_graph} = this.props;
    if (Object.keys(progress_graph).length == 0) {
      return null;
    }
    // ニュースフィード・関連ゴールタブ切り替え時にリサイズ&ツールチップ再表示
    if (this.state.flush) {
      this.state.chart.flush();
      const last_index = progress_graph.data[2].length - 2;
      this.state.chart.tooltip.show({mouse:[last_index, 50], index: last_index});
    }

    return (
      <div className="panel panel-default p_10px">
        <span className="js-flush-chart hidden" onClick={this.flushChart}></span>
        <h3 className="progressGraph-title">
          {__("PROGRESS")}
          <span className="progressGraph-title-sub">{__("All goal's total you have.")}</span>
        </h3>
        <div id="chart"></div>
        <div className="progressGraph-footer">
          <div className="progressGraph-footer-left"><span>{progress_graph.start_date}</span></div>
          <div className="progressGraph-footer-right"><span>{progress_graph.end_date}</span></div>
        </div>
        <div className="progressGraph-legend">
          <span className="progressGraph-legend-mark mod-sweetspot"></span>
          <span> : {__("Sweet Spot(Drive for it!)")}</span>
        </div>
      </div>
    )
  }
}

Graph.propTypes = {
  progress_graph: React.PropTypes.object
};
Graph.defaultProps = {progress_graph: {}};
