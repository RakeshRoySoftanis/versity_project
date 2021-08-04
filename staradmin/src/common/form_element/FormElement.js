import React, { Component , useState , useEffect  } from 'react'
import DatePicker from "react-datepicker";
import {Line} from 'react-chartjs-2';
import { Form , Modal , Button  , InputGroup, FormControl, Dropdown} from 'react-bootstrap';
import { FormElements , selectOptions } from '../../services/CommonService';
import cogoToast from 'cogo-toast';
import Dropzone from 'react-dropzone';
import { PhotoshopPicker } from 'react-color';
import DateTimePicker from '../datetimepicker/DateTimePicker';
import ReactTags from 'react-tag-autocomplete';
import Select from 'react-select';

//dropzone
const thumbsContainer = {
  display: 'flex',
  flexDirection: 'row',
  flexWrap: 'wrap',
  marginTop: 16
};

const thumb = {
  display: 'inline-flex',
  borderRadius: 2,
  border: '1px solid #eaeaea',
  marginBottom: 8,
  marginRight: 8,
  width: 100,
  height: 100,
  padding: 4,
  boxSizing: 'border-box'
};

const img = {
  display: 'block',
  width: 'auto',
  height: '100%'
};

const thumbInner = {
  display: 'flex',
  minWidth: 0,
  overflow: 'hidden'
};

const DefaultDatepicker = () => {
  const [startDate, setStartDate] = useState(new Date());
  return (
    <DatePicker selected={startDate} onChange={date => setStartDate(date)} className="form-control" style={{'z-index':3}}/>
  );
};

class FormRepeater extends Component {
  constructor(){
      super();
      this.state = {
          users: [{id: 1, name: ''}]
      }
      this.inputChangeHandler = this.inputChangeHandler.bind(this);
  }

  inputChangeHandler(event, index) {
      const users = this.state.users;
      users[index].name = event.target.value;
      this.setState(users);;
  }

  addUserInput = () => {
      const users =[...this.state.users];
      users.push({id: this.state.users[this.state.users.length - 1].id + 1, name: ''});
      this.setState({users: users});
  }

  deleteUser(index) {
      const users =[...this.state.users];
      users.splice(index, 1);
      this.setState({users: users});
  }

  render() {
      return (
          <form className="form-inline" onSubmit={(event)=>{event.preventDefault();}}>
              <div className="d-flex flex-column">
                 <div className="table-responsive">
                  <table className="table">
                  <thead>
                      <tr>
                        <th>User</th>
                        <th>Date Time.</th>
                        <th>Textarea</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                  { this.state.users.map((user, index) => {
                      return (
                      <tr key={user.id}>
                        <td>
                          <input 
                              type="text"
                              className="form-control"
                              placeholder="Add user"
                              value={user.name}
                              name="user[]"
                              onChange={(event) => this.inputChangeHandler(event, index)}
                          />
                        </td>
                        <td>
                        <DefaultDatepicker />
                        </td>
                        <td>
                        <Form.Group>
                          <textarea className="form-control" id="exampleTextarea1" rows="4"></textarea>
                        </Form.Group>
                        </td>
                        <td>
                        {(index > 0) ? <button className="btn btn-danger btn-sm icon-btn ml-2" onClick={()=>this.deleteUser(index)}><i className="mdi mdi-delete"></i></button> : <button className="btn btn-info btn-sm icon-btn ml-2 mb-2" onClick={ this.addUserInput } ><i className="mdi mdi-plus"></i></button> }
                        </td>
                      </tr>
                  
                      )
                  })}
                  </tbody>
                  </table>
                </div>
              </div>
              
          </form>
      )
  }
}

const DefaultClockpicker = () => {
  const [startDate, setStartDate] = useState(new Date());
  return (
      <DatePicker
      selected={startDate}
      onChange={date => setStartDate(date)}
      showTimeSelect
      showTimeSelectOnly
      timeIntervals={15}
      timeCaption="Time"
      dateFormat="h:mm aa"
      className="form-control"
      />
  );
};

function PsColorPickerhead() {
  const [colorhead, setColorhead] = useState('');
  const [menuOpenHead,toggleMenuHead]= useState(false);
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

function FormElement(props) {
  const [validated, setvalidated] = useState(false);
  //dropzone
  const [folders, setFolders] = useState([]);
  const [fileData, setFile] = useState([]);
  const [files, setFiles] = useState([]);
  
  //select2
  const [selecOption, setSelecOption] = useState([]);
  const [page, setPage] = useState(1);
  const [pagebool, setPagebool] = useState(true);
  const [areaData, setAreaData] = useState([]);
  const [areaDatas, setAreaDatas] = useState([]);
  const [areaOptions, setAreaOptions] = useState([]);

  // data
  const HandleSubmit = async (event) => {
    const form = event.currentTarget;
    event.preventDefault();
    if (form.checkValidity() === false) {
        event.stopPropagation();
    }
    setvalidated(true);
    
    //save after validation success
    if (form.checkValidity() === true) {
        const data = new FormData(event.target);
        await FormElements(data).then(response => {
            cogoToast.success(response.message, {position: 'top-right'});
        }).catch( error => {
            cogoToast.error(error.response.data.message, {position: 'top-right'});
        });
        setvalidated(false);
    }
  }

  //dropzone

  const thumbs = files.map( (file  , key)  => (
    <div style={thumb} key={key}>
      <div style={thumbInner}>
        <img
          src={file.preview}
          style={img}
          alt="File preview"
        />
      </div>
    </div>
));

  const onDrop = async(acceptedFiles , files) => {
    // preview
    setFiles(
        [ ...files, ...acceptedFiles.map(file => Object.assign(file, {
            preview: URL.createObjectURL(file)
        })) ]
    )

    acceptedFiles.map((file) => {
        let fd = new FormData();
        fd.append('file',file);
        FormElements(fd).then(res => {
            cogoToast.success(res.message, {position: 'top-right'});
        }).catch( err => {
            cogoToast.error(err.response.data.message, {position: 'top-right'});
        });
    });
  }

  const selectOption = async() =>{
    if (pagebool) {
      setPagebool(!pagebool)
      selectOptions(page,"none").then(res => {
        setSelecOption( [ ...selecOption , ...res.data ] )
        setPage(res.page)
        if (res.data.length == 0) {
          setPagebool(false)
        }else{
          setPagebool(true)
        }
      }).catch( err => err);
    }

  }

  const fetchData = async (ordr) => {
    selectOptions(1,"none").then(res => {
      setSelecOption(res.data)
      setPage(res.page)
      setAreaData( 
        {
          labels: res.charts.labels ,
          datasets: [{
            data: res.charts.data ,
          }]
        }
      )
    }).catch( err => err);
  };

  const searchOption = async(event) => {
    let searchValue = event.target.value;
    if(searchValue.length > 0){
      selectOptions(1,searchValue).then(res => {
        setSelecOption(res.data)
        setPage(res.page)
      }).catch( err => err);
    }
  }

  useEffect(() => {
    fetchData();
    setAreaOptions(
      {
        plugins: {
          filler: {
            propagate: true
          }
        }
      }
    )

  }, []);

  return(
    <>
     {/* Checkbox & Radio button Controls */}
    <div className="row">
      <div className="col-md-6 grid-margin stretch-card">
        <div className="card">
          <div className="card-body">
            <h4 className="card-title">Checkbox & Radio button Controls</h4>
            <form noValidate onSubmit={HandleSubmit}>
              <div className="row">
                <div className="col-md-6">
                  <Form.Group>
                    <div className="form-check">
                      <label className="form-check-label">
                        <input type="checkbox" className="form-check-input" name="checkbox[]" defaultValue="1" />
                        <i className="input-helper"></i>
                        Default
                      </label>
                    </div>
                    <div className="form-check">
                      <label className="form-check-label">
                        <input type="checkbox" defaultChecked className="form-check-input" name="checkbox[]" defaultValue="2" />
                        <i className="input-helper"></i>
                        Checked
                      </label>
                    </div>
                  </Form.Group>
                </div>
                <div className="col-md-6">
                  <Form.Group>
                    <div className="form-check">
                      <label className="form-check-label">
                        <input type="radio" className="form-check-input" name="optionsRadios"  value="1"/>
                        <i className="input-helper"></i>
                        Default
                      </label>
                    </div>
                    <div className="form-check">
                      <label className="form-check-label">
                        <input type="radio" className="form-check-input" name="optionsRadios" value="2" defaultChecked />
                        <i className="input-helper"></i>
                        Selected
                      </label>
                    </div>
                  </Form.Group>
                </div>
              </div>

              <Modal.Footer className="fleex-wrap">
                  <Button type="submit" className="btn btn-primary">
                  <i className="mdi mdi-check"></i> 
                  Submit
                  </Button>
              </Modal.Footer>

            </form>
          </div>
        </div>
      </div>

      {/* Dropzone */}

    
      <div className="col-6">
          <div className="card grid-margin stretch-card">
              <div className="card-body">
                  <Dropzone onDrop={onDrop} >
                      {({getRootProps, getInputProps}) => (
                          <section>
                          <div {...getRootProps()} style={{minHeight: '200px', border: '2px dotted #0000004d', background: '#F8F8F8' }} className="text-center">
                              <input {...getInputProps()} />
                              <i className="fa fa-cloud-upload" style={{fontSize: '40px',margin: '10px', color: '#751F58'}}></i><br />
                              <span style={{fontSize: '15px', fontWeight:'bold', color: '#751F58'}}>Drag and Drop or <a style={{color:'#3D9AFB'}}>Browse</a></span>
                              <br />
                              <span style={{fontSize: '15px', fontWeight:'bold', color: '#751F58'}}>Upload your files here and we will organize them for you</span>
                              <br />
                              <span style={{fontSize: '15px', fontWeight:'bold', color: '#f70097'}}>Note: Please upload less than 28MB files</span>
                              <div className="clr"></div>

                          </div>
                          <aside style={thumbsContainer}>
                              {thumbs}
                          </aside>
                          </section>
                      )}
                  </Dropzone>
              </div>
          </div>
      </div>

      <div className="col-6 grid-margin stretch-card">
          <div className="card">
              <div className="card-body">
                  <p className="card-description"> datepicker</p>
                  <DefaultDatepicker />
                  <p className="card-description">Date Time</p>
                  <DateTimePicker FieldName="End_DateTime" placeHolder="Date Time"  />
                  <p className="card-description">A simple clockpicker</p>
                  <DefaultClockpicker />
              </div>
          </div>
      </div>

      <div className="col-lg-6 grid-margin grid-margin-lg-0">
          <div className="card-body">
              <h4 className="card-title">Header Color</h4>
              <PsColorPickerhead />
          </div>
      </div>

      <div className="col-lg-6 grid-margin grid-margin-lg-0">
          <div className="card">
            <div className="card-body">
                <h4 className="card-title">Input Tag</h4>
                <p className="card-description">Type to add a new tag </p>
                <TagsInput />
            </div>
          </div>
      </div>

      <div className="col-lg-6">
        <div className="card">
            <div className="card-body" >
              <h4 className="card-title">React Select</h4>
              <Select 
                  name="select2name"
                  options={ selecOption }
                  onMenuScrollToBottom={selectOption}
                  onKeyDown ={searchOption}
              />
            </div>
        </div>
      </div>

      <div className="col-lg-6">
        <div className="card">
          <div className="card-body">
              <h4 className="card-title">Form Repeater</h4>
              <p className="card-description">Click the add button to repeat the form</p>
              <FormRepeater />
          </div>
        </div>
      </div>

      <div className="col-md-6 grid-margin stretch-card">
          <div className="card">
              <div className="card-body">
                  <h4 className="card-title">Area Chart</h4>

                  {
                    areaData.length == undefined ? <Line data={areaData} options={areaOptions} /> : ""
                  }
                  
              </div>
          </div>
      </div>

    </div>
    </>
  )
}


class TagsInput extends Component {
  constructor (props) {
      super(props)
   
      this.state = {
        tags: [
          { id: 1, name: "London" },
          { id: 2, name: "Canada" },
          { id: 3, name: "Australia" },
          { id: 4, name: "Mexico" },
        ],
        suggestions: [
          { id: 5, name: "India" },
          { id: 6, name: "United States of America" },
          { id: 7, name: "Italy" },
          { id: 8, name: "Japan" },
          { id: 9, name: "China" },
          { id: 10, name: "Russia" }
        ]
      }
    }
   
    handleDelete (i) {
      const tags = this.state.tags.slice(0)
      tags.splice(i, 1)
      this.setState({ tags })
    }
   
    handleAddition (tag) {
      const tags = [].concat(this.state.tags, tag)
      this.setState({ tags })
    }
   
    render () {
      return (
        <ReactTags
          tags={this.state.tags}
          suggestions={this.state.suggestions}
          allowNew={true}
          handleDelete={this.handleDelete.bind(this)}
          handleAddition={this.handleAddition.bind(this)}
          
          />
      )
    }
}

export default FormElement;
