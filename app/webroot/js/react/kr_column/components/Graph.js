import React from "react";
import c3 from "c3";
import "c3/c3.css";

export default class Graph extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      flush: false
    };
    this.flushChart = this.flushChart.bind(this);
    // this.tooltipContents = this.tooltipContents.bind(this);
  }

  flushChart(e) {
    this.setState({
      flush: true
    });
  }

  _renderChart(data) {
    let chart = c3.generate({
      size: {
        height: 200
      },
      data: {
        // x: "x",
        columns: [
          ['data1', 4, 5.4, 6.8, 8.2, 9.6, 11, 12.4, 13.8, 15.2, 16.6, 18, 19.4, 20.8, 22.2, 23.6, 25, 26.4, 27.8, 29.2, 30.6, 32, 33.4, 34.8, 36.2, 37.6, 39, 40.4, 41.8, 43.2, 44.6],
          ['data2', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30],
          ['Current', 0.8, 2, 3.4, 5, 8, 10, 10.5, 11, 13.3, 13, 13.1, 14, 15, 19, 19, 19, 19.1, 20, 29.2, 34],
          // ['x', "2017-01-01", "2017-01-02", "2017-01-03", "2017-01-04", "2017-01-05", "2017-01-06", "2017-01-07", "2017-01-08", "2017-01-09", "2017-01-10", "2017-01-11", "2017-01-12", "2017-01-13", "2017-01-14", "2017-01-15", "2017-01-16", "2017-01-17", "2017-01-18", "2017-01-19", "2017-01-20", "2017-01-21", "2017-01-22", "2017-01-23", "2017-01-24", "2017-01-25", "2017-01-26", "2017-01-27", "2017-01-28", "2017-01-29", "2017-01-30"]
        ],
        types: {
          data1: 'area',
          data2: 'area',
          "Current": 'line',
        },
        colors: {
          data1: '#d4f2fc',
          data2: '#fff',
          "Current": '#4a98ef',
        },
      },
      point: {
        r: function (d) {
          return d.value == 34 ? 5 : 0;
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
          // type: 'timeseries',
        },
        y: {
          show: false
        }
      },
      tooltip: {
        contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
          var $$ = this, config = $$.config, CLASS = $$.CLASS,
            titleFormat = config.tooltip_format_title || defaultTitleFormat,
            nameFormat = config.tooltip_format_name || function (name) {
                return name;
              },
            valueFormat = config.tooltip_format_value || defaultValueFormat,
            text, i, title, value, name, bgcolor;

          // You can access all of data like this:
          for (i = 0; i < d.length; i++) {
            if (!(d[i] && (d[i].value || d[i].value === 0))) {
              return "";
            }

            // ADD
            if (d[i].name !== 'Current') {
              continue;
            }

            value = valueFormat(d[i].value, d[i].ratio, d[i].id, d[i].index);



            if (!text) {
              text = "<table class='" + CLASS.tooltip + "'>";
            }
            // name = nameFormat(d[i].name);
            name = __("Current");
            bgcolor = $$.levelColor ? $$.levelColor(d[i].value) : color(d[i].id);

            text += "<tr class='" + CLASS.tooltipName + "-" + d[i].id + "'>";
            text += "<td class='name'><span style='background-color:" + bgcolor + "'></span>" + name + "</td>";
            text += "<td class='value'>" + value + "%</td>";
            text += "</tr>";
          }
          return text + "</table>";
        }
      }
    });
    return chart;
  }

  componentDidMount() {
    let chart = this._renderChart(this.props.data);
    chart.tooltip.show({index: 19});
  }


  render() {
    this._renderChart(this.props.data);
    return (
      <div className="panel panel-default p_10px" id="ProgressChartBox">
        <span className="js-flush-chart" onClick={this.flushChart}></span>
        <h3 className="progressGraph-title">
          {__("PROGRESS")}
          <span className="progressGraph-title-sub">{__("All goal's total you have.")}</span>
        </h3>
        <div id="chart"></div>
        <div className="progressGraph-footer">
          <div className="progressGraph-footer-left"><span>March 1</span></div>
          <div className="progressGraph-footer-right"><span>March 30</span></div>
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
  progress_graph: React.PropTypes.array
};
Graph.defaultProps = {progress_graph: []};
