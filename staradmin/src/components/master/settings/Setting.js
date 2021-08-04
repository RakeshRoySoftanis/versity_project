import React, { useEffect, useState } from 'react';
import BreadcrumbsCustom from '../../BreadcrumbsCustom';
import { withRouter } from 'react-router-dom';
import { Form , Button ,Spinner } from 'react-bootstrap';
import { setting , settingsUpdate , settingsLogoUpdate , ColorSetting } from '../../../services/MasterService';
import { PhotoshopPicker} from 'react-color';
import { InputGroup, FormControl, Dropdown} from 'react-bootstrap';
import cogoToast from 'cogo-toast';
import InputMask from "react-input-mask";

function Setting(props) {

    const [validated, setvalidated] = useState(false);
    const [submitDisabled, setsubmitDisabled] = useState(false); 
    const [logo, setLogo] = useState([]);  
    const [zoho_auth, setZoho_auth] = useState([]); 
    const [phone, setPhone] = useState([]); 
    const [quickLink, setQuickLink] = useState([]); 
    const [quickTitle, setQuickTitle] = useState([]); 
    const [loading, setLoading] = useState(false);
    const [apiBaseUrl, setApiBaseUrl] = useState('');
    const [menuOpenHead,toggleMenuHead]= useState(false);
    const [menuOpenHeding,setmenuOpenHeding]= useState(false);
    const [menuOpenNav,togglemenuOpenNav]= useState(false);
    const [menuOpenFont,toggleMenuFont]= useState(false);
    const [menuOpenFooter,toggleMenuFooter]= useState(false);

    const [colorhead, setColorhead] = useState('');
    const [colorheading, setColorheading] = useState('');
    const [colornav, setColornav] = useState('');
    const [colorfont, setColorfont] = useState('');
    const [colorfooter, setColorfooter] = useState('');

    function PsColorPickerhead() {
        return (
            <InputGroup className="mb-3">
                <FormControl
                    placeholder="Color Value"
                    aria-label="Recipient's username"
                    aria-describedby="basic-addon2"
                    value={colorhead}
                    onChange={(event) => setColorhead(event.target.value)}
                    name="headerColor"
                />
                <InputGroup.Append>
                    <Dropdown show={menuOpenHead}>
                        <Dropdown.Toggle id="dropdown-basic" className="px-3" style={{height: '100%', backgroundColor:colorhead, color: colorhead, borderColor: colorhead}} onClick={()=> {toggleMenuHead(!menuOpenHead)}}>
                        </Dropdown.Toggle>
                        <Dropdown.Menu>
                            <PhotoshopPicker color={colorhead} onChange={(colorhead) => setColorhead(colorhead.hex)} onAccept={()=>{toggleMenuHead(false)}} onCancel={()=>{toggleMenuHead(false)}} />
                        </Dropdown.Menu>
                    </Dropdown>
                </InputGroup.Append>
            </InputGroup>
        );
    }

    function PsColorPickerheading() {
        return (
            <InputGroup className="mb-3">
                <FormControl
                    placeholder="Color Value"
                    aria-label="Recipient's username"
                    aria-describedby="basic-addon2"
                    value={colorheading}
                    onChange={(event) => setColorheading(event.target.value)}
                    name="headingcolor"
                />
                <InputGroup.Append>
                    <Dropdown show={menuOpenHeding}>
                        <Dropdown.Toggle id="dropdown-basic" className="px-3" style={{height: '100%', backgroundColor:colorheading, color: colorheading, borderColor: colorheading}} onClick={()=> {setmenuOpenHeding(!menuOpenHeding)}}>
                        </Dropdown.Toggle>
                        <Dropdown.Menu>
                            <PhotoshopPicker color={colorheading} onChange={(colorheading) => setColorheading(colorheading.hex)} onAccept={()=>{setmenuOpenHeding(false)}} onCancel={()=>{setmenuOpenHeding(false)}} />
                        </Dropdown.Menu>
                    </Dropdown>
                </InputGroup.Append>
            </InputGroup>
        );
    }

    function PsColorPickerNav() {
        return (
            <InputGroup className="mb-3">
                <FormControl
                    placeholder="Color Value"
                    aria-label="Recipient's username"
                    aria-describedby="basic-addon2"
                    value={colornav}
                    onChange={(event) => setColornav(event.target.value)}
                    name="menubarcolor"
                />
                <InputGroup.Append>
                    <Dropdown show={menuOpenNav}>
                        <Dropdown.Toggle id="dropdown-basic" className="px-3" style={{height: '100%', backgroundColor:colornav, color: colornav, borderColor: colornav}} onClick={()=> {togglemenuOpenNav(!menuOpenNav)}}>
                        </Dropdown.Toggle>
                        <Dropdown.Menu>
                            <PhotoshopPicker color={colornav} onChange={(colornav) => setColornav(colornav.hex)} onAccept={()=>{togglemenuOpenNav(false)}} onCancel={()=>{togglemenuOpenNav(false)}} />
                        </Dropdown.Menu>
                    </Dropdown>
                </InputGroup.Append>
            </InputGroup>
        );
    }

    function PsColorPickerfont() {
        return (
            <InputGroup className="mb-3">
                <FormControl
                    placeholder="Color Value"
                    aria-label="Recipient's username"
                    aria-describedby="basic-addon2"
                    value={colorfont}
                    onChange={(event) => setColorfont(event.target.value)}
                    name="fontcolor"
                />
                <InputGroup.Append>
                    <Dropdown show={menuOpenFont}>
                        <Dropdown.Toggle id="dropdown-basic" className="px-3" style={{height: '100%', backgroundColor:colorfont, color: colorfont, borderColor: colorfont}} onClick={()=> {toggleMenuFont(!menuOpenFont)}}>
                        </Dropdown.Toggle>
                        <Dropdown.Menu>
                            <PhotoshopPicker color={colorfont} onChange={(colorfont) => setColorfont(colorfont.hex)} onAccept={()=>{toggleMenuFont(false)}} onCancel={()=>{toggleMenuFont(false)}} />
                        </Dropdown.Menu>
                    </Dropdown>
                </InputGroup.Append>
            </InputGroup>
        );
    } 

    function PsColorPickerfooter() {
        return (
            <InputGroup className="mb-3">
                <FormControl
                    placeholder="Color Value"
                    aria-label="Recipient's username"
                    aria-describedby="basic-addon2"
                    value={colorfooter}
                    onChange={(event) => setColorfooter(event.target.value)}
                    name="footer"
                />
                <InputGroup.Append>
                    <Dropdown show={menuOpenFooter}>
                        <Dropdown.Toggle id="dropdown-basic" className="px-3" style={{height: '100%', backgroundColor:colorfooter, color: colorfooter, borderColor: colorfooter}} onClick={()=> {toggleMenuFooter(!menuOpenFooter)}}>
                        </Dropdown.Toggle>
                        <Dropdown.Menu>
                            <PhotoshopPicker color={colorfooter} onChange={(colorfooter) => setColorfooter(colorfooter.hex)} onAccept={()=>{toggleMenuFooter(false)}} onCancel={()=>{toggleMenuFooter(false)}} />
                        </Dropdown.Menu>
                    </Dropdown>
                </InputGroup.Append>
            </InputGroup>
        );
    }

    const fetchData = async () => {
        setLoading(true);
        setting().then(res => {
            setLogo(res.logo);
            setZoho_auth(res.zoho_auth);
            setPhone(res.phoneNo);
            setQuickTitle( res.quick_title );
            setQuickLink( res.quick_link );
            setApiBaseUrl(res.base_url);

            setColorhead(res.logo.head);
            setColorheading(res.logo.heading);
            setColornav(res.logo.nav);
            setColorfont(res.logo.font);
            setColorfooter(res.logo.footer);

        }).catch( err => err);
        setLoading(false);
    };

    useEffect(() => {
        fetchData()
      }, []);

      const deleteQuickLink = async(e) => {
          e.target.parentElement.parentElement.parentElement.parentElement.remove();
      }

      const addQuickLink = async() =>{ 
        var node = document.getElementById("show_quick").innerHTML;
        var d = document.createElement( 'div' );
        d.classList.add("row")
        d.innerHTML = node.replace('<i class="mdi mdi-plus-circle-outline" style="font-size: 30px;"></i>' , '');
        var p = document.getElementById('myList');
        p.appendChild(d);
        p.children[p.children.length - 1].children[2].addEventListener("click", deleteQuickLink)
      }

      

    //save function Setting

   const SubmitSetting = async (event) => {
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

        
        await settingsUpdate(data).then(response => {
            cogoToast.success(response.message, {position: 'top-right'});
            setsubmitDisabled(false)
        }).catch( error => {
          setsubmitDisabled(false)
            cogoToast.error(error.response.message, {position: 'top-right'});
        });
        setvalidated(false);
    }
  }

  //Logo Setting
  const SubmitLogoSetting = async (event) => {
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
        await settingsLogoUpdate(data).then(response => {
            fetchData()
            cogoToast.success(response.message, {position: 'top-right'});
            setsubmitDisabled(false)
        }).catch( error => {
          setsubmitDisabled(false)
            cogoToast.error(error.response.message, {position: 'top-right'});
        });
        setvalidated(false);
    }
  }

  // Submit Color Setting

  const SubmitColorSetting = async (event) => {
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

        await ColorSetting(data).then(response => {
            fetchData()
            cogoToast.success(response.message, {position: 'top-right'});
            setsubmitDisabled(false)
        }).catch( error => {
          setsubmitDisabled(false)
            cogoToast.error(error.response.message, {position: 'top-right'});
        });
        setvalidated(false);
    }
  }

  const listData =  quickLink.map( (qLink , key)=> 
        <div key={key}>
            <div className="row" >
                <div className="col-md-5 p-1" key={key}>
                    <Form.Group>
                        <label htmlFor="exampleInputName1">Quick Link Heading</label>
                        <Form.Control type="text" className="form-control"  placeholder="Name" name="quick_title[]" defaultValue={ quickTitle[key] } />
                        <Form.Control.Feedback type="invalid">Please enter Email.</Form.Control.Feedback>
                    </Form.Group>
                </div>

                <div className="col-md-5 p-1">
                    <Form.Group>
                        <label htmlFor="exampleInputName1">Quick Link</label>
                        <Form.Control type="text" className="form-control"  placeholder="Name" name="quick_link[]" defaultValue={qLink} />
                        <Form.Control.Feedback type="invalid">Please enter Email.</Form.Control.Feedback>
                    </Form.Group>
                </div>

                <div className="col-md-2 p-1">
                    <Form.Group>
                        <label></label>
                        <div> 
                            <i className="mdi mdi-delete" style={{ fontSize:"30px" }} onClick={ deleteQuickLink } ></i> 
                        </div>
                    </Form.Group>
                </div>

            </div>
        </div>
        
        );
  
  return (
    <div>
        <div className="row chngFormGroup">
            <div className="col-12 grid-margin stretch-card">
                <div className="card">
                <h4 className="card-title zvc_color_blue" style={{padding: 25, marginBottom: 0, borderBottom: 'none'}}>Settings </h4>
                    <div className="card-body">
                        <Form className="forms-sample" noValidate validated={validated} onSubmit={ SubmitSetting }>
                            <div className="row">
                                <div className="col-md-6 p-1">
                                    <Form.Group>
                                        <label htmlFor="exampleInputName1">Full Name</label>
                                        <Form.Control type="text" name="name" className="form-control"  placeholder="Full Name"  required defaultValue={logo.name} />
                                        <Form.Control.Feedback type="invalid">Please enter Full Name.</Form.Control.Feedback>
                                    </Form.Group>
                                </div>
                                <div className="col-md-6 p-1">
                                    <Form.Group>
                                        <label htmlFor="exampleInputName1">Phone </label>
                                        <InputMask mask="999 999 9999" required  name="phone"  className="form-control" value={phone.toString() } onChange={(e)=> setPhone(e.target.value) } />
                                        <Form.Control.Feedback type="invalid">Please enter Phone.</Form.Control.Feedback>
                                    </Form.Group>
                                </div>
                                <div className="col-md-6 p-1">
                                    <Form.Group>
                                        <label htmlFor="exampleInputName1">Fax</label>
                                        <Form.Control type="text" className="form-control" required  placeholder="Name" name="fax" defaultValue={logo.fax} />
                                        <Form.Control.Feedback type="invalid">Please enter Fax.</Form.Control.Feedback>
                                    </Form.Group>
                                </div>
                                <div className="col-md-6 p-1">
                                    <Form.Group>
                                        <label htmlFor="exampleInputName1">Email</label>
                                        <Form.Control type="email" className="form-control" required  placeholder="Email" name="email" defaultValue={logo.email} />
                                        <Form.Control.Feedback type="invalid">Please enter Email.</Form.Control.Feedback>
                                    </Form.Group>
                                </div>
                                <div className="col-md-6 p-1">
                                    <Form.Group>
                                        <label htmlFor="exampleInputName1">Website</label>
                                        <Form.Control type="text" className="form-control" required  placeholder="Website" name="website" defaultValue={logo.website} />
                                        <Form.Control.Feedback type="invalid">Please enter Website.</Form.Control.Feedback>
                                    </Form.Group>
                                </div>
                                <div className="col-md-6 p-1">
                                    <Form.Group>
                                        <label htmlFor="exampleInputName1">Youtube Link</label>
                                        <Form.Control type="text" className="form-control"  placeholder="Youtube Link" name="youtubelink" defaultValue={logo.youtubelink} />
                                    </Form.Group>
                                </div>
                                <div className="col-md-6 p-1">
                                    <Form.Group>
                                        <label htmlFor="exampleInputName1">Client Dashboard</label>
                                        <Form.Control type="text" className="form-control" required  placeholder="Client Dashboard" name="client_dashboard" defaultValue={logo.client_dashboard} />
                                        <Form.Control.Feedback type="invalid">Please enter Client Dashboard.</Form.Control.Feedback>
                                    </Form.Group>
                                </div>

                                <div style={{clear: 'both', height: 30}} /> 

                                {listData}
                                <span id="myList" />

                                <div>
                                    <div className="row" id="show_quick"> 
                                        <div className="col-md-5 p-1" >
                                            <Form.Group>
                                                <label htmlFor="exampleInputName1">Quick Link Heading</label>
                                                <Form.Control type="text" className="form-control"  placeholder="Name" name="quick_title[]" required />
                                                <Form.Control.Feedback type="invalid">Please enter Quick Link Heading.</Form.Control.Feedback>
                                            </Form.Group>
                                        </div>

                                        <div className="col-md-5 p-1">
                                            <Form.Group>
                                                <label htmlFor="exampleInputName1">Quick Link</label>
                                                <Form.Control type="text" className="form-control"  placeholder="Name" name="quick_link[]" required />
                                                <Form.Control.Feedback type="invalid">Please enter Quick Link.</Form.Control.Feedback>
                                            </Form.Group>
                                        </div>

                                        <div className="col-md-2 p-1">
                                            <Form.Group>
                                                <label></label>
                                                <div>
                                                    <i className="mdi mdi-delete" style={{ fontSize:"30px" }} onClick={ deleteQuickLink } ></i> 
                                                    <i className="mdi mdi-plus-circle-outline" style={{ fontSize:"30px" }} onClick={ addQuickLink } ></i> 
                                                </div>
                                            </Form.Group>
                                        </div>
                                    </div>
                                </div>


                                <div className="col-md-6 p-1">
                                    <Form.Group>
                                        <h4>Address Information</h4>
                                    </Form.Group>
                                </div>

                                <div className="form-row">
                                    <div className="col-md-6 form-group">
                                        <label>Street</label>
                                        <input type="text" name="street" className="form-control"  placeholder="street" defaultValue={logo.street} />                                
                                    </div>
                                    <div className="col-md-6 form-group">
                                        <label>City</label>
                                        <input type="text" name="city" className="form-control"  placeholder="city" defaultValue={logo.city} />                                
                                    </div>
                                    <div className="col-md-3 form-group">
                                        <label>State</label>
                                        <input type="text" name="state" className="form-control"  placeholder="State" defaultValue={logo.state} />                                
                                    </div>
                                    <div className="col-md-3 form-group">
                                        <label>Zip</label>
                                        <input type="text" name="zip" className="form-control"  placeholder="zip" defaultValue={logo.zip} />                                
                                    </div>
                                    <div className="col-md-6 form-group">
                                        <label>Country</label>
                                        <input type="text" name="country" className="form-control" placeholder="country" defaultValue={logo.country} />                                
                                    </div>
                                </div>


                            </div> 

                            <Button type="submit" className="btn btn-primary" disabled={submitDisabled } >
                                { submitDisabled &&
                                <Spinner animation="border" role="status" size="sm" className="mr-2">
                                    <span className="sr-only">Loading...</span>
                                </Spinner>
                                }
                                { !submitDisabled && <i className="mdi mdi-check"></i> }
                                Save
                            </Button>
                        </Form>
                    </div>
                </div>
            </div>

            <div className="col-12 grid-margin stretch-card ">
                <div className="card">
                <h4 className="card-title zvc_color_blue" style={{padding: 25, marginBottom: 0, borderBottom: 'none'}}>Logo </h4>
                    <div className="card-body">
                        <Form className="forms-sample" noValidate validated={validated} onSubmit={ SubmitLogoSetting }>
                            <div className="row">
                                <div className="col-md-6 p-1">
                                    <Form.Group>
                                        <input type="file" name="logo" className="form-control" />  
                                        <p style={{color: '#d64a4a', fontSize: 14, padding: 10, paddingBottom: 0}}>Logo Image Size width: 230px &amp; Height: 62px</p>

                                    </Form.Group>
                                </div>

                                <div className="col-md-6 p-1 logo_lg">
                                    <img src={ apiBaseUrl + "/psettings/logo/" + logo.logo } style={{width: "max-content", height: "auto"}} />
                                </div>


                                <div className="col-md-6 p-1">
                                    <Form.Group>
                                        <input type="file" name="minilogo" className="form-control" />  
                                        <p style={{color: '#d64a4a', fontSize: 14, padding: 10, paddingBottom: 0}}>Mini Logo Image Size width: 32px & Height: 32px</p>
                                    </Form.Group>
                                </div>

                                <div className="col-md-6 p-1">
                                    <img src={ apiBaseUrl + "/psettings/logo/" + logo.minilogo } style={{width: "max-content", height: "auto"}} />
                                </div>


                            </div> 

                            <Button type="submit" className="btn btn-primary" disabled={submitDisabled } >
                                { submitDisabled &&
                                <Spinner animation="border" role="status" size="sm" className="mr-2">
                                    <span className="sr-only">Loading...</span>
                                </Spinner>
                                }
                                { !submitDisabled && <i className="mdi mdi-check"></i> }
                                Save
                            </Button>
                        </Form>
                    </div>
                </div>
            </div>

            <div className="col-12 grid-margin stretch-card">
                <div className="card">
                <h4 className="card-title zvc_color_blue" style={{padding: 25, marginBottom: 0, borderBottom: 'none'}}> Color & Preferences </h4>
                    <div className="card-body">
                        <Form className="forms-sample" noValidate validated={validated} onSubmit={ SubmitColorSetting }>
                            <div className="row">
                                <div className="col-lg-6 grid-margin grid-margin-lg-0">
                                    <div className="card-body">
                                        <h4 className="card-title">Header Color</h4>
                                        <PsColorPickerhead />
                                    </div>
                                </div>

                                <div className="col-lg-6 grid-margin grid-margin-lg-0">
                                    <div className="card-body">
                                        <h4 className="card-title">Heading Color </h4>
                                        <PsColorPickerheading />
                                    </div>
                                </div>

                                <div className="col-lg-6 grid-margin grid-margin-lg-0">
                                    <div className="card-body">
                                        <h4 className="card-title">Nav/Menu Bar Color</h4>
                                        <PsColorPickerNav />
                                    </div>
                                </div>

                                <div className="col-lg-6 grid-margin grid-margin-lg-0">
                                    <div className="card-body">
                                        <h4 className="card-title">Font/Text Color</h4>
                                        <PsColorPickerfont />
                                    </div>
                                </div>

                                <div className="col-lg-6 grid-margin grid-margin-lg-0">
                                    <div className="card-body">
                                        <h4 className="card-title">Footer Color</h4>
                                        <PsColorPickerfooter />
                                    </div>
                                </div>

                            </div> 

                            <Button type="submit" className="btn btn-primary" disabled={submitDisabled } >
                                { submitDisabled &&
                                <Spinner animation="border" role="status" size="sm" className="mr-2">
                                    <span className="sr-only">Loading...</span>
                                </Spinner>
                                }
                                { !submitDisabled && <i className="mdi mdi-check"></i> }
                                Save
                            </Button>
                        </Form>
                    </div>
                </div>
            </div>




        </div>
      </div>
  )
  
}

export default withRouter(Setting) 