import cogoToast from 'cogo-toast';
import React, { useState } from 'react';
import { Link, Redirect } from 'react-router-dom';
import { PUBLIC_URL } from '../../constants';
import { forgetPasswordMail } from '../../services/CommonService';

function ForgetPassword(props) {

    const [validated, setvalidated] = useState(false);
    const [submitDisabled, setsubmitDisabled] = useState(false); 

    //save function
   
   const handleSubmit = async (event) => {
    const form = event.currentTarget;
    event.preventDefault();
    if (form.checkValidity() === false) {
        event.stopPropagation();
    }
    setvalidated(true);
    
    //save after validation success
    if (form.checkValidity() === true) {
      setsubmitDisabled(true)

        const data = new FormData(event.target);
        await forgetPasswordMail(data).then(response => {
            cogoToast.success(response.message, {position: 'top-right'});
            
        }).catch( error => {
          setsubmitDisabled(false)
            cogoToast.error(error.response.message, {position: 'top-right'});
        });
        setvalidated(false);
    }
  }

  if(localStorage.getItem('token')){
    return <Redirect to="/dashboard"></Redirect>
  }

  return (
    <div>
      <div className="d-flex align-items-stretch auth auth-img-bg h-100">
        <div className="row flex-grow">
          <div className="col-lg-6 d-flex align-items-center justify-content-center">
            <div className="auth-form-transparent text-left p-3">
              <div className="brand-logo">
              </div>
              <form className="pt-3" onSubmit={handleSubmit}>

                <div className="form-group">
                  <label>Email</label>
                  <div className="input-group">
                    <div className="input-group-prepend bg-transparent">
                      <span className="input-group-text bg-transparent border-right-0">
                        <i className="mdi mdi-account-outline text-primary"></i>
                      </span>
                    </div>
                    <input name="publicPath" defaultValue={ window.location.origin } type="hidden" />
                    <input type="email" className="form-control form-control-lg border-left-0"  placeholder="Email" name="email" required  />
                  </div>
                </div>
                <div className="my-3">
                  <button type="submit" className="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" >Send Password</button>
                </div>
                {/* <div className="text-center mt-4 font-weight-light">
                  Don't have an account? <Link to={`#`} className="text-primary">Create</Link>
                </div> */}
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
  
}


export default ForgetPassword 




