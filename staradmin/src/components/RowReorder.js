import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import { PUBLIC_URL } from '../constants'
import Moment from 'react-moment';
import NumberFormat from 'react-number-format';

function RowReorder(props) {

  let el
  let array = [];
  let slNo
  let total_tblnumber = document.getElementsByClassName('rdt_TableBody').length;

  if (props.tableSerialNo == total_tblnumber ) {
     slNo = props.tableSerialNo - 1;
     el = document.getElementsByClassName('rdt_TableBody')[slNo].children[0].children;
      for (let index = 1; index < el.length; index++) {
        let compStyles =  window.getComputedStyle( el[index] )
        array[index - 1] = compStyles.getPropertyValue('display') == "none" ? "flex" : "none";
      }

  }else if (props.tableSerialNo < total_tblnumber ) {
      el = document.getElementsByClassName('rdt_TableBody')[props.tableSerialNo].children[0].children;
      for (let index = 1; index < el.length; index++) {
        let compStyles =  window.getComputedStyle( el[index] )
        array[index - 1] = compStyles.getPropertyValue('display') == "none" ? "flex" : "none";
      }
  }

  return (
    <>
      {
        props.columns.map((datas , key) =>
          <div className="row row_rorder" key={key} style={{ display: array[key] }} >
            {
              (datas.name == "Action") ? "" : <div className="col-4"> <b> { datas.name } </b>  </div>
            }

            {(() => {
              if (typeof datas.customUse !== 'undefined' && datas.customUse =="moment" ) {
                return (
                  <div className="col-8">   
                          <Moment format="MMM DD, YYYY hh:mm A">
                    { props.data[datas.selector] }
                    </Moment>
                </div>
                )
              } else if (typeof datas.customClass !== 'undefined' ) {
                return (
                  <div className={datas.customClass}>{ props.data[datas.selector] } </div>
                )
              }else if (typeof datas.json_index !== 'undefined' ) {
                return (
                  <div className="col-8">  { JSON.parse(props.data[datas.json_index])[datas.json_value]   } </div>
                )
              }
              else if (typeof datas.hasPrefix !== 'undefined' ) {
                return (
                  <div className="col-8"> { datas.hasPrefix } { props.data[datas.selector] }  </div>
                )
              }

              else if (typeof datas.NumberFormat !== 'undefined' ) {
                return (
                  <div className="col-8"> <NumberFormat value={ props.data[datas.selector] } displayType={'text'} thousandSeparator={true} prefix={'$'} /> </div>
                  
                )
              }
              
              else {
                return (
                  <div className="col-8"> { props.data[datas.selector] } </div>
                )
              }
            })()}

          </div>
        )
      }
      <br></br>
    </>
  )
  
}

export default RowReorder




