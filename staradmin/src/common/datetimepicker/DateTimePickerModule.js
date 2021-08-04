import React from "react";
import Datetime from "react-datetime";
import "react-datetime/css/react-datetime.css";

export default function DateTimePickerModule(props) {

  if (typeof props.required != "undefined" && props.required != ""  ) {
    let inputProps = {
      placeholder: props.placeHolder,
      required: true,
      name: props.FieldName,
      autoComplete: "off"
    };
    if(props.value != undefined && props.value != ""){
       return <Datetime utc="true" inputProps={inputProps} className={  props.class !=undefined ? props.class : ""  } initialValue={props.value} locale="en-US" dateFormat="YYYY-MM-DD" timeFormat="HH:mm" />;
    }else{
       return <Datetime utc="true" inputProps={inputProps} className={  props.class !=undefined ? props.class : ""  } locale="en-US" dateFormat="YYYY-MM-DD" timeFormat="HH:mm" />;
    }

  }else{
    let inputProps = {
      placeholder: props.placeHolder,
      required: false,
      name: props.FieldName,
      autoComplete: "off"
    };

    if(props.value != undefined && props.value != ""){
        return <Datetime utc="true" inputProps={inputProps} className={  props.class !=undefined ? props.class : ""  } initialValue={props.value} locale="en-US" dateFormat="YYYY-MM-DD" timeFormat="HH:mm" />;
   }else{
        return <Datetime utc="true" inputProps={inputProps} className={  props.class !=undefined ? props.class : ""  }  locale="en-US" dateFormat="YYYY-MM-DD" timeFormat="HH:mm" />;
   }
   
  }
  
}
