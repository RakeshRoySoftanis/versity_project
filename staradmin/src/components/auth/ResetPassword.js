import cogoToast from 'cogo-toast';
import React, { useState } from 'react';
import { useParams , Redirect } from 'react-router-dom';
import { resetPassword } from '../../services/CommonService';

function ResetPassword(props) {

    const [validated, setvalidated] = useState(false);
    const [submitDisabled, setsubmitDisabled] = useState(false); 
    const pswd_info = React.useRef(null);
    const letter = React.useRef(null);
    const length = React.useRef(null);
    const capital = React.useRef(null);
    const number = React.useRef(null);
    const space = React.useRef(null);
    const resetPawd = React.useRef(null);
    const { id } = useParams();

    //save function

    const changePasswordInut = async(event) => {
      pswd_info.current.style.display = "block";
      let pswd;
      var validation = true;
      pswd = event.target.value

      //validate the length
      if ( pswd.length < 8 ) {
          length.current.classList.remove("valid")
          length.current.classList.add("invalid")
          validation = false;
      } else {
          length.current.classList.remove("invalid")
          length.current.classList.add("valid")
      }

      //validate letter
      if ( pswd.match(/[A-z]/) ) {
          letter.current.classList.remove("invalid")
          letter.current.classList.add("valid")
      } else {
          letter.current.classList.remove("valid")
          letter.current.classList.add("invalid")
          validation = false;
      }

      //validate capital letter
      if ( pswd.match(/[A-Z]/) ) {
          capital.current.classList.remove("invalid")
          capital.current.classList.add("valid")
      } else {
          capital.current.classList.remove("valid")
          capital.current.classList.add("invalid")
          validation = false;
      }

      //validate number
      if ( pswd.match(/\d/) ) {
          number.current.classList.remove("invalid")
          number.current.classList.add("valid")
      } else {
          number.current.classList.remove("valid")
          number.current.classList.add("invalid")
          validation = false;
      }
      
      //validate space
      if ( pswd.match(/[^a-zA-Z0-9\-\/]/) ) {
          space.current.classList.remove("invalid")
          space.current.classList.add("valid")
      } else {
          space.current.classList.remove("valid")
          space.current.classList.add("invalid")
          validation = false;
      }

      if (validation) {
        pswd_info.current.style.display = "none";
        resetPawd.current.style.display = "block";
      }else{
        resetPawd.current.style.display = "none";
      }

    }
   
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
        await resetPassword(data).then(response => {
            cogoToast.success(response.message, {position: 'top-right'});
            return <Redirect to="/dashboard"></Redirect>
        }).catch( error => {
          setsubmitDisabled(false)
            cogoToast.error(error.response.data.message, {position: 'top-right'});
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

                <div>
                  <input name="publicPath" defaultValue={ window.location.origin } type="hidden" />
                  <input name="token" defaultValue={ id } type="hidden" />
                  <div className="form-group">
                    <label htmlFor="password" className="control-label">Password</label>
                    <div >
                      <div className="input-group">
                        <input type="password" className="form-control" id="password" name="password" onChange={changePasswordInut} />
                        <div className="input-group-append " id="view_button3">
                        </div>
                        <div className="input-group-append " id="view_button4" style={{display: 'none'}}>
                          <span className="input-group-text bg-transparent">
                            <i className="mdi mdi-eye " />
                          </span>
                        </div>
                      </div>
                      <div className="aro-pswd_info">
                        <div id="pswd_info" ref = {pswd_info}>
                          <h4>Password requirements</h4>
                          <ul>
                            <li id="letter" ref = {letter} className="invalid">At least <strong>one letter</strong></li>
                            <li id="capital" ref = {capital} className="invalid">At least <strong>one capital letter</strong></li>
                            <li id="number" ref = {number} className="invalid">At least <strong>one number</strong></li>
                            <li id="length" ref = {length} className="invalid">At least <strong>8 characters</strong></li>
                            <li id="space" ref = {space} className="invalid">At least <strong>one special character [~,!,@,#,$,%,^,&amp;,*,-,=,.,;,']</strong></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="form-group">
                    <label htmlFor="password-confirm" className=" control-label">Confirm Password</label>
                    <div >
                      <input id="password-confirm" type="password" className="form-control" name="password_confirmation" required />
                    </div>
                  </div>
                  <div className="form-group">
                    <div>
                      <button type="submit" className="btn btn-primary"  ref={resetPawd} >
                        Reset Password
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
  
}


export default ResetPassword 




