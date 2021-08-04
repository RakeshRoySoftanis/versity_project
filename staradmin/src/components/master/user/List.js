import React, { useEffect, useMemo, useState } from 'react';
import BreadcrumbsCustom from '../../BreadcrumbsCustom';
import DataTable from "react-data-table-component";
import { Link, withRouter } from 'react-router-dom';
import Swal from 'sweetalert2'
import { Form, Modal, Button, Spinner, Col } from 'react-bootstrap';
import { getUserList, saveUserByMaster, getUserById, updateUser, deleteUser , insert_master_user} from '../../../services/MasterService';
import { MASTER_PUBLIC_URL } from '../../../constants';
import cogoToast from 'cogo-toast';
import RowReorder from '../../RowReorder';


const FilterComponent = ({ filterText, onFilter, onClear , onAddModalShow }) => (
    <>
        <div className="row">
            <Form.Group className="col-7 float-right table-search">
                <Form.Control id="search" type="text" placeholder="Search...." value={filterText} onChange={onFilter} />
            </Form.Group>
            <div className="col-1 p-0">
                <Button type="button" className="btn btn-secondary tableSearchClear" onClick={onClear}>X</Button>
            </div>

            <div className="col-4 addButtonTbl" >
                <Button onClick={onAddModalShow}  className="btn-sm"> <i className="mdi mdi-plus-circle-outline" style={{fontSize: "large" }}></i> Add  </Button>
            </div>
        </div>
    </>
);

function ExpandedComponent(data) {
    return (
        <div className="row">
            <div className="col-6"> <b>Name</b>  </div>
            <div className="col-6">{data.data.name} </div>

            <div className="col-6"> <b>Email </b>  </div>
            <div className="col-6">{data.data.email} </div>

            <div className="col-6"> <b>Phone</b>  </div>
            <div className="col-6">{data.data.phone} </div>
        </div>
    );
}

const AdvancedPaginationTable = () => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(false);
    const [totalRows, setTotalRows] = useState(0);
    const [perPage, setPerPage] = useState(10);
    const [currentPage, setCurrentPage] = useState(1);
    const [filterText, setFilterText] = useState('');
    const [sortField, setSortField] = useState('id');
    const [sortOrder, setSortOrder] = useState('asc');

    //update
    const [dataEdit, setDataEdit] = useState([]);
    const [editMdShow, setEditMdShow] = useState(false);
    const [validated, setvalidated] = useState(false);
    const [submitDisabledupdate, setsubmitDisabledupdate] = useState(false);
    const [submitDisabled, setsubmitDisabled] = useState(false);

    const [mdShow, setMdShow] = useState(false);
    //delete row

    const deleteRow = async (id) => {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                deleteUser(id).then(res => {
                    fetchData(1)
                    swalWithBootstrapButtons.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                }).catch(err => err);

            }

            if (result.dismiss == "cancel") {
                swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Your imaginary file is safe :)',
                    'error'
                )
            }

        })

    }

    {/* get data for update */ }
    const editFunction = async (id) => {
        getUserById(id).then(res => {
            setEditMdShow(true)
            setDataEdit(res.pgData);
        }).catch(err => err);
    }

    //update data
    const updateSubmit = async (event) => {
        const form = event.currentTarget;
        event.preventDefault();
        if (form.checkValidity() === false) {
            event.stopPropagation();
            setEditMdShow(true)
        }
        setvalidated(true);

        //save after validation success
        if (form.checkValidity() === true) {
            setsubmitDisabledupdate(true)
            setEditMdShow(false)

            const data = new FormData(event.target);

            await updateUser(data).then(response => {
                cogoToast.success(response.message, { position: 'top-right' });
                setsubmitDisabledupdate(false)
                fetchData(1)
            }).catch(error => {
                setsubmitDisabledupdate(false)
                cogoToast.error(error.response.data.message, { position: 'top-right' });
            });
            setvalidated(false);
        }
    }
    const fetchData = async (page, size = perPage, search = filterText, sField = sortField, sOrder = sortOrder) => {
        setLoading(true);

        getUserList(page, size, search, sortField, sortOrder).then(res => {
            setData(res.pgData);
            setTotalRows(res.totalRows);
            
        }).catch(err => err);

        setLoading(false);

    };

    const columns = [
        {
            selector: 'name',
            name: 'Name',
            sortable: true
        }, {
            selector: 'email',
            name: 'Email',
            sortable: true,
            hide: "md"
        }, {
            selector: 'phone',
            name: 'Phone',
            sortable: true,
            hide: "sm"
        }, {
            name: "Action",
            cell: row => <>
                <Button className="nav-link btn btn-inverse-primary" style={{ margin: "auto" }} onClick={() => editFunction(row.id)} >
                    <i className="fa fa-edit"></i>
                </Button>

                <Button className="nav-link btn btn-inverse-danger" style={{ margin: "auto" }} onClick={() => { deleteRow(row.id) }} >
                    <i className="mdi mdi-delete"></i>
                </Button>

                {/* modal for update start */}

                <Modal
                    show={editMdShow}
                    onHide={() => setEditMdShow(false)}
                    aria-labelledby="example-modal-sizes-title-md"
                >

                    <Modal.Header closeButton>
                        <Modal.Title>Edit Admin User</Modal.Title>
                    </Modal.Header>
                    <Form noValidate validated={validated} onSubmit={updateSubmit} >
                        <Modal.Body>
                            <Form.Row>
                                <input name="id" value={dataEdit.id} type="hidden" />
                                <Form.Group as={Col} md="12" controlId="validationCustom07">
                                    <Form.Label>Name</Form.Label>
                                    <Form.Control type="text" name="name" placeholder="Enter User Name" required defaultValue={dataEdit.name} />
                                    <Form.Control.Feedback type="invalid">
                                        Please provide a valid Name.
                                    </Form.Control.Feedback>
                                </Form.Group>

                                <Form.Group as={Col} md="12" controlId="validationCustom08">
                                    <Form.Label>Email</Form.Label>
                                    <Form.Control type="email" name="email" placeholder="Enter Email" required defaultValue={dataEdit.email} />
                                    <Form.Control.Feedback type="invalid">
                                        Please provide a valid Email.
                                    </Form.Control.Feedback>
                                </Form.Group>


                                <Form.Group as={Col} md="12" controlId="validationCustom09">
                                    <Form.Label>Phone</Form.Label>
                                    <Form.Control type="number" name="phone" placeholder="Enter Phone" required defaultValue={dataEdit.phone} />
                                    <Form.Control.Feedback type="invalid">
                                        Please provide a valid Phone.
                                    </Form.Control.Feedback>
                                </Form.Group>

                                <Form.Group as={Col} md="12" controlId="validationCustom10">
                                    <Form.Label>Password</Form.Label>
                                    <Form.Control type="password" name="password" placeholder="*******" required />
                                    <Form.Control.Feedback type="invalid">
                                        Please provide a valid Password.
                                    </Form.Control.Feedback>
                                </Form.Group>

                            </Form.Row>
                        </Modal.Body>

                        <Modal.Footer className="fleex-wrap">
                            <Button type="submit" className="btn btn-primary" disabled={submitDisabledupdate} >
                                {submitDisabledupdate &&
                                    <Spinner animation="border" role="status" size="sm" className="mr-2">
                                        <span className="sr-only">Loading...</span>
                                    </Spinner>
                                }
                                <i className="mdi mdi-check"></i>
                                Update
                            </Button>
                            <Button variant="light m-2" onClick={() => setEditMdShow(false)} > <i className="mdi mdi-close"></i> Cancel</Button>
                        </Modal.Footer>
                    </Form>

                </Modal>
            </>

        }
    ];



    const handlePageChange = page => {
        fetchData(page);
        setCurrentPage(page);
    };

    const handlePerRowsChange = async (newPerPage, page) => {
        fetchData(page, newPerPage);
        setPerPage(newPerPage);
    };

    const handleSort = async (column, sortDirection) => {
        setSortField(column.selector);
        setSortOrder(sortDirection);
    };

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
            await insert_master_user(data).then(response => {
                cogoToast.success(response.message, { position: 'top-right' });
                setMdShow(false)
                setsubmitDisabled(false)
                fetchData(1)
            }).catch(error => {
                setsubmitDisabled(false)
                cogoToast.error(error.response.data.message, { position: 'top-right' });
            });
            setvalidated(false);
        }
    }

    useEffect(() => {
        fetchData(1);
    }, [filterText, sortOrder, sortField]);

    const subHeaderComponentMemo = useMemo(() => {
        const handleClear = () => {
            if (filterText) {
                setFilterText('');
            }
        };

        const AddModalShow = () => {
            setMdShow(true);
      };

        return (
        <>
            <FilterComponent onFilter={e => setFilterText(e.target.value)} onClear={handleClear} filterText={filterText} onAddModalShow={AddModalShow} />

            <Modal
                show={mdShow}
                onHide={() => setMdShow(false)}
                aria-labelledby="example-modal-sizes-title-md"
            >

                <Modal.Header closeButton>
                    <Modal.Title>Add New Admin User</Modal.Title>
                </Modal.Header>
                <Form noValidate validated={validated} onSubmit={handleSubmit} >
                    <Modal.Body>

                        <Form.Row>
                            <Form.Group as={Col} md="12" controlId="validationCustom03">
                                <Form.Label>Name</Form.Label>
                                <Form.Control type="text" name="name" placeholder="Enter User Name" required />
                                <Form.Control.Feedback type="invalid">
                                    Please provide a valid Name.
                                </Form.Control.Feedback>
                            </Form.Group>

                            <Form.Group as={Col} md="12" controlId="validationCustom04">
                                <Form.Label>Email</Form.Label>
                                <Form.Control type="email" name="email" placeholder="Enter Email" required />
                                <Form.Control.Feedback type="invalid">
                                    Please provide a valid Email.
                                </Form.Control.Feedback>
                            </Form.Group>


                            <Form.Group as={Col} md="12" controlId="validationCustom05">
                                <Form.Label>Phone</Form.Label>
                                <Form.Control type="number" name="phone" placeholder="Enter Phone" required />
                                <Form.Control.Feedback type="invalid">
                                    Please provide a valid Phone.
                                </Form.Control.Feedback>
                            </Form.Group>

                            <Form.Group as={Col} md="12" controlId="validationCustom06">
                                <Form.Label>Password</Form.Label>
                                <Form.Control type="password" name="password" placeholder="*******" required />
                                <Form.Control.Feedback type="invalid">
                                    Please provide a valid Password.
                                </Form.Control.Feedback>
                            </Form.Group>

                        </Form.Row>

                    </Modal.Body>
                    <Modal.Footer className="fleex-wrap">

                        <Button type="submit" className="btn btn-primary" disabled={submitDisabled} >
                            {submitDisabled &&
                                <Spinner animation="border" role="status" size="sm" className="mr-2">
                                    <span className="sr-only">Loading...</span>
                                </Spinner>
                            }
                            <i className="mdi mdi-check"></i>
                            Save
                        </Button>

                        <Button variant="light m-2" onClick={() => setMdShow(false)} > <i className="mdi mdi-close"></i> Cancel</Button>
                    </Modal.Footer>
                </Form>

            </Modal>
        </>
        );
    }, [filterText , mdShow]);

    return (

        <DataTable
            title=""
            columns={columns}
            data={data}
            progressPending={loading}
            pagination
            paginationServer
            paginationTotalRows={totalRows}
            paginationDefaultPage={currentPage}
            onChangeRowsPerPage={handlePerRowsChange}
            onChangePage={handlePageChange}
            subHeader
            subHeaderComponent={subHeaderComponentMemo}
            onSort={handleSort}
            sortServer
            paginationRowsPerPageOptions={[10, 30, 50]}
            expandableRows
            expandableRowsComponent={<RowReorder columns={columns} tableSerialNo="0" />} //if there is multiple table in one page we need tableSerialNo
            striped
        />
    );
};




function List(props) {

    return (
        <div>
            <div className="row">
                <div className="col-12">
                    <div className="card">
                        <div className="card-body">
                            <div className="row">
                                <div className="col-12">
                                    <div className="row">
                                        <div className="col-6"> <h4>User</h4>   </div>
                                    </div>
                                    <div className="mt-2">

                                        {/* data list here */}
                                        <div className="col-md-12 p-0">

                                            <AdvancedPaginationTable />

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    )

}

export default withRouter(List)



