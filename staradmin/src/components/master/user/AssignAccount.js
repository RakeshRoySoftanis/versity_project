import React, { useEffect, useState } from 'react';
import BreadcrumbsCustom from '../../BreadcrumbsCustom';
import { withRouter , useParams, Link } from 'react-router-dom';
import { Form , Button ,Spinner   } from 'react-bootstrap';
import { userAssignAccount , saveUserByMaster } from '../../../services/MasterService';
import cogoToast from 'cogo-toast';
import { MASTER_PUBLIC_URL } from '../../../constants';

function AssignAccount(props) {

    const { id }  = useParams();
    const [submitDisabled, setsubmitDisabled] = useState(false); 
    const [loading, setLoading] = useState(false);
    const [data, setData] = useState([]);
    const [alreadyAssigned, setAlreadyAssigned] = useState([]);
    const [userData, setUserData] = useState([]);

    const SelectAll = async (event) => {
      var inputs, i;
      inputs = document.getElementsByTagName('input');
      for (i = 0; i < inputs.length; ++i) {
          inputs[i].checked = event.target.checked
      }
    }

    // get data
    const fetchData = async (id = id) => {
        setLoading(true);
        userAssignAccount(id = id).then(res => {
          setData(res.accounts);
          setAlreadyAssigned(res.alreadyAssigned);
          setUserData(res.userData);
        }).catch( error => {
          setsubmitDisabled(false)
          cogoToast.error(error.response.message, {position: 'top-right'});
        });
  
        setLoading(false);
    };

    useEffect(() => {
        fetchData(id);
    }, []);

    const handleSubmit = async (event) => {
      setsubmitDisabled(true)
      const form = event.currentTarget;

      event.preventDefault();
      //save after validation success
  
      const data = new FormData(event.target);
      await saveUserByMaster(data).then(response => {
          cogoToast.success(response.message, {position: 'top-right'});
          setsubmitDisabled(false)
      }).catch( error => {
        setsubmitDisabled(false)
          cogoToast.error(error.response.message, {position: 'top-right'});
      });
    }
    let listItems;

    const chngeBox = async (event) =>{
      var inputs, i;
      inputs = document.getElementsByTagName('input');
      for (i = 0; i < inputs.length; ++i) {
        if (inputs[i].checked == false) {
          inputs[0].checked = false
        }
      }
    }

  return (
    
    <div>                      
    <BreadcrumbsCustom
         title="Assign Accounts" 
         title_right = "Home"
         title_right_two = "Users"
    />

      <div className="row">
            <div className="col-12">
                <div className="card">
                    <div className="card-body">
                        <div className="row">
                            <div className="col-12">
                               <Link to={`${MASTER_PUBLIC_URL}/users`} className="btn btn-primary float-right" > <i className="mdi mdi-keyboard-backspace"></i>  Back </Link>
                            </div>
                            <div className="col-12"> 
                              <div className="form-check"> 
                                <label className="form-check-label">
                                  <input type="checkbox" className="form-check-input 2" onClick={ (event) => { SelectAll(event)  }   } />
                                  <i className="input-helper"></i>
                                  Select All
                                </label>
                              </div>
                              <Form onSubmit={ handleSubmit } >
                                <input type="hidden" name="id" defaultValue={id} />
                                <div className="row" >
                                {
                                  data.map((datas , key) =>
                                    <div className="col-md-4 stretch-card" key={key}>  { ( alreadyAssigned.includes(datas.module_id) ? 
                                      (
                                      <div className="form-check">
                                        <label className="form-check-label">
                                          <input type="checkbox" defaultChecked className="form-check-input" defaultValue={datas.module_id} name="accounts[]" onClick={chngeBox} />
                                          <i className="input-helper"></i>
                                          {datas.Account_Name} 
                                        </label>
                                      </div>
                                      ) : (
                                        <div className="form-check">
                                          <label className="form-check-label">
                                            <input type="checkbox" className="form-check-input" defaultValue={datas.module_id} name="accounts[]" onClick={chngeBox} />
                                            <i className="input-helper"></i>
                                            {datas.Account_Name}
                                          </label>
                                        </div>
                                      ) 
                                    )}
                                    </div>
                                  )
                                }
                                </div>

                                <div className="fleex-wrap">
                                  <Button type="submit" className="btn btn-primary" disabled={submitDisabled } >
                                    { submitDisabled &&
                                    <Spinner animation="border" role="status" size="sm" className="mr-2">
                                        <span className="sr-only">Loading...</span>
                                    </Spinner>
                                    }
                                    <i className="mdi mdi-check"></i> 
                                    Save
                                  </Button>
                                </div>
                              </Form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
  )
  
}

export default withRouter(AssignAccount) 



