import React from "react";
import Datetime from "react-datetime";
import "react-datetime/css/react-datetime.css";

export default function DateTimePicker(props) {

  if (typeof props.required == "undefined" ) {
    let inputProps = {
      placeholder: props.placeHolder,
      required: true,
      name: props.FieldName,
      autoComplete: "off"
    };

    return <Datetime inputProps={inputProps} initialValue={props.value} locale="en-US" dateFormat="YYYY-MM-DD" timeFormat="HH:mm" />;
  }else{
    let inputProps = {
      placeholder: props.placeHolder,
      required: false,
      name: props.FieldName,
      autoComplete: "off"
    };

    return <Datetime inputProps={inputProps} initialValue={props.value} locale="en-US" dateFormat="YYYY-MM-DD" timeFormat="HH:mm" />;
  }
  
}
