import React, { Component } from 'react';
import 'c3/c3.css';
import { Redirect } from 'react-router-dom';


export class Dashboard extends Component {
  constructor(props) {
    super(props);
    this.state = {
    }
  } 
  
  render () {
    // redirect to login
    if(!localStorage.getItem('token')){
      return <Redirect to="/login"></Redirect>
    }


    return (
      <>

      </> 
    );
  }
}
export default Dashboard;