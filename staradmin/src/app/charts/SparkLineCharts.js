import React, { Component } from 'react';
import { Sparklines, SparklinesLine, SparklinesBars, SparklinesSpots, SparklinesReferenceLine } from 'react-sparklines';


export class SparkLineCharts extends Component {
  render() {
    return (
      <div>
        <div class="page-header">
          <h3 class="page-title"> Sparkline </h3>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="!#" onClick={event => event.preventDefault()}>Charts</a></li>
              <li class="breadcrumb-item active" aria-current="page">Sparkline</li>
            </ol>
          </nav>
        </div>
        <div class="row">
          <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Line chart</h4>
                <Sparklines data={[5, 10, 5, 20]}>
                  <SparklinesLine color="blue" />
                </Sparklines>
              </div>
            </div>
          </div>
          <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Bar chart</h4>
                <Sparklines data={[5, 10, 5, 20, 15, 30, 13, 24, 16]}>
                  <SparklinesBars />
                </Sparklines>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Spot </h4>
                <Sparklines data={[5, 10, 5, 20, 15, 30, 13, 24, 16]}>
                  <SparklinesLine style={{ fill: "none" }} />
                  <SparklinesSpots />
                </Sparklines>
              </div>
            </div>
          </div>
          <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Reference Line</h4>
                <Sparklines data={[5, 10, 5, 20, 15, 30, 13, 24, 16]}>
                  <SparklinesLine />
                  <SparklinesReferenceLine type="mean" />
                </Sparklines>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default SparkLineCharts
