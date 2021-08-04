import React, { Component, useState } from 'react';
import {Form, Col , Spinner , Button } from 'react-bootstrap';
import { withRouter } from 'react-router-dom';
import DateTimePicker from '../../common/datetimepicker/DateTimePicker';
import DatePicker from 'react-datepicker';
import InputMask from "react-input-mask";

const DefaultDatepicker = () => {
    const [startDate, setStartDate] = useState(new Date());
    return (
      <DatePicker selected={startDate} onChange={date => setStartDate(date)} className="form-control" style={{'z-index':3}} name="task_due_date" />
    );
};

export class ContactAddView extends Component {

    constructor(props){
        super(props);
        this.state = {
            dispalyAddNote: "none",
            NoteImage: "Upload Attachment",
            CompletedCall : "block",
            ScheduleCall : "block",
        }

        this.inputFileRef = React.createRef();
        this.onFileChange = this.handleFileChange.bind( this );
        this.onBtnClick = this.handleBtnClick.bind( this );
        this.cancelButton =  this.cancelButton.bind( this );
        this.callDetail =  this.callDetail.bind( this );
    }

    handleFileChange( e ) {
        let file = e.target.files[0].name;
        this.setState({
            NoteImage : file
        });
    }

    callDetail(e){

        this.setState({
            CompletedCall : "none",
            ScheduleCall : "none",
        });
        
        if (e.target.value == "Completed") {
            this.setState({
                CompletedCall : "block",
                ScheduleCall : "block",
            });
        }

        if (e.target.value == "Scheduled") {
            this.setState({
                ScheduleCall : "block"
            });
        }
    }
    
    handleBtnClick() {
        this.inputFileRef.current.click();
        this.setState({
            dispalyUploadIcon : "none"
        });
    }

    cancelButton(){
        this.setState({ NoteImage : "" });
    }
      
    render() {
        const Parent_Id_ID = this.props.Parent_Id_ID;
        const type = this.props.type;
        const NoteImages = this.props.NoteImage;

        const { dispalyAddNote  , NoteImage } = this.state;

        if( type == "Note") {
            //note form start here
            return (
                <div>
                    <Form.Row>
                        <Form.Group as={Col} md="7" controlId="validationCustom03">
                            <input type="hidden" name="module_id" defaultValue={this.props.module_id}  required/>
                            <input type="hidden" name="owner_id" defaultValue={this.props.Owner_ID} required/>
                            
                            <Form.Control type="text" name="Note_Title" placeholder="Add Title..." required />
                            <Form.Control.Feedback type="invalid">
                                Please provide a valid Title.
                            </Form.Control.Feedback>
                        </Form.Group>
                        <div className="clr"></div>
                        <Form.Group as={Col} md="7" controlId="validationCustom04">
                            <textarea className="form-control" placeholder="Add Description..." name="notes" rows="6" required></textarea>
                            <Form.Control.Feedback type="invalid">
                                Please provide a valid Description.
                            </Form.Control.Feedback>
                        </Form.Group>
                        <div className="clr"></div>

                        <Form.Group as={Col} md="5">
                            <div style={{ display: dispalyAddNote }}>
                                <input className="form-control"
                                    type="file"
                                    ref={this.inputFileRef}
                                    onChange={this.onFileChange}
                                    name="notesImage"
                                />
                            </div>
                            <div className="btn btn-primary" onClick={this.onBtnClick}> <i className="mdi mdi-attachment"  style={{ marginRight:"12px" , cursor:"pointer" }} title="File Attachment" ></i> { NoteImages || NoteImage} </div>
                        </Form.Group>

                        <Form.Group as={Col} md="4" className="add_notes" controlId="validationCustom06" style={{ marginLeft: "60px" }} >
                            <Button type="submit" className="btn btn-primary" disabled={this.props.submitDisabled || false } >
                                { this.props.submitDisabled &&
                                <Spinner animation="border" role="status" size="sm" className="mr-2">
                                    <span className="sr-only">Loading...</span>
                                </Spinner>
                                }
                                { !this.props.submitDisabled && <i className="mdi mdi-check"></i> }
                                Save
                            </Button>
                            <Button variant="light m-2" type="reset" onClick={this.cancelButton} > <i className="mdi mdi-close" ></i> Cancel</Button>
                        </Form.Group>
                    </Form.Row>    
                </div>
            )
        }
    }
}

export default withRouter(ContactAddView)
