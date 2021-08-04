import React, { useEffect, useMemo, useState } from 'react';
import { Form, Modal, Button } from 'react-bootstrap';
import DataTable from 'react-data-table-component';
import { Link, withRouter } from 'react-router-dom';
import BreadcrumbsCustom from '../BreadcrumbsCustom';
import { MASTER_PUBLIC_URL } from '../../constants';
import { attemptLoggedIn, getPortalClientList, permission_use_role, updateuserRoleLayoutData } from '../../services/MasterService';
import cogoToast from 'cogo-toast';
import RowReorder from '../RowReorder';

const FilterComponent = ({ filterText, onFilter, onClear }) => (
    <>
        <div className="row">
            <Form.Group className="col-10 float-right table-search">
                <Form.Control id="search" type="text" placeholder="Search...." value={filterText} onChange={onFilter} />
            </Form.Group>
            <div className="col-2 p-0">
                <Button type="button" className="btn btn-secondary tableSearchClear" onClick={onClear}>X</Button>
            </div>
        </div>
    </>
);


function ExpandedComponent(data) {
    return (
        <div className="row">
            <div className="col-6"> <b>Name</b>  </div>
            <div className="col-6">{data.data.Full_Name} </div>

            <div className="col-6"> <b>Account Name </b>  </div>
            <div className="col-6">{data.data.Account_Name} </div>

            <div className="col-6"> <b>Email</b>  </div>
            <div className="col-6">{data.data.Email} </div>

            <div className="col-6"> <b>Phone</b>  </div>
            <div className="col-6">{data.data.Phone} </div>
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
    const [uerRoleLayoutModal, setUerRoleLayoutModal] = useState(false);
    const [Module_id, setModule_id] = useState('');
    const [uerRoleLayoutdata, setUerRoleLayoutdata] = useState([]);
    const [layout_role, setPortal_layout_role] = useState([]);


    const fetchData = async (page, size = perPage, search = filterText, sField = sortField, sOrder = sortOrder) => {
        setLoading(true);
        getPortalClientList(page, size, search, sField, sOrder).then(res => {
            setData(res.pgData);
            setTotalRows(res.totalRows);
        }).catch(err => err);
        setLoading(false);
    };


    const loggedInPortal = (module_id) => {
        const data = {
            module_id: module_id,
        }

        attemptLoggedIn(data).then(response => {
            localStorage.removeItem("token");
            localStorage.setItem('token', response.token);
            cogoToast.success(response.message, { position: 'top-right' });
            window.open('/', "_blank");
        }).catch(error => {
            cogoToast.error(error.response.data.message, { position: 'top-right' });
        });

    }

    const uerRoleLayout = async (e, module_id, action, portal_layout_role) => {
        
        
        setPortal_layout_role(portal_layout_role)
        setLoading(true);
        setUerRoleLayoutModal(true);
        permission_use_role().then(res => {
            setUerRoleLayoutdata(res.data)
            setModule_id(module_id)
        }).catch(err => err);
        setLoading(false);
    }


    const updateuerRoleLayout = (event) => {

        let fields = document.getElementById("selectbox").value
        let sel = document.getElementById("selectbox")
        let module_id = document.getElementById("module_id").value

        var opt;
        for (var i = 0, len = sel.options.length; i < len; i++) {
            opt = sel.options[i];
            if (opt.selected === true) {
                break;
            }
        }
        let fieldval = opt.innerHTML

        if (fields != "true") {

            updateuserRoleLayoutData(module_id, fields, fieldval).then(response => {
                cogoToast.success(response.message, { position: 'top-right' });
                fetchData(1);
            }).catch(err => {
                cogoToast.error(err.response.data.message, { position: 'top-right' });
            });

        } else {
            cogoToast.success("Something went wrong", { position: 'top-right' });

        }

        setUerRoleLayoutModal(false)

    }

    const columns = [
        {
            selector: 'Full_Name',
            name: 'Name',
            sortable: true,
            width: "15%"
        }, {
            selector: 'Account_Name',
            name: 'Account Name',
            sortable: true,
            hide: "md",
            width: "15%"
        }, {
            selector: 'Email',
            name: 'Email',
            sortable: true,
            hide: "sm",
            width: "20%"
        }, {
            selector: 'Phone',
            name: 'Phone',
            sortable: true,
            hide: "sm",
            width: "10%"
        }, {
            selector: 'portal_layout_role_name',
            name: 'User Role Name',
            sortable: true,
            hide: "sm",
            width: "10%"
        }, {
            name: "Action",
            width: "40%",
            cell: row => <>

                <button type="button" className="btn btn-inverse-primary mr-2" onClick={(e) => uerRoleLayout(e, row.module_id, "foriegn_key" , row.portal_layout_role )} ><i className="mdi mdi-eye "></i> Assign User Role</button>

                <button type="button" className="btn btn-inverse-primary mr-2" onClick={() => loggedInPortal(row.module_id)}><i className="mdi mdi-login "></i> Login</button>

                {/* <Link className="btn btn-inverse-primary mr-2" target="_blank" to={`${PUBLIC_URL}/master/client-login/`+row.module_id}>
                    <i className="mdi mdi-login "></i> Login
                </Link>  */}

                <Link className="btn btn-inverse-primary" to={`${MASTER_PUBLIC_URL}/master-assign-vaults/` + row.module_id}>
                    <i className="mdi mdi-lock-outline "></i> Assign Vaults
                </Link>

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

    useEffect(() => {
        fetchData(1);
    }, [filterText, sortOrder, sortField]);

    const subHeaderComponentMemo = useMemo(() => {
        const handleClear = () => {
            if (filterText) {
                setFilterText('');
            }
        };
        return (<>
            <FilterComponent onFilter={e => setFilterText(e.target.value)} onClear={handleClear} filterText={filterText} />
        </>
        );
    }, [filterText]);

    return (

        <>
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
                paginationRowsPerPageOptions={[5, 10, 30, 50]}
                expandableRows
                expandableRowsComponent={<RowReorder columns={columns} tableSerialNo="0" />} //if there is multiple table in one page we need tableSerialNo
                striped
            />

            <Modal
                show={uerRoleLayoutModal}
                onHide={() => setUerRoleLayoutModal(false)}
                aria-labelledby="example-modal-sizes-title-md"
            >
                <Modal.Header closeButton>
                    <Modal.Title> Assgin User layout Role </Modal.Title>
                </Modal.Header>
                {/* <Form noValidate validated={validated} onSubmit={updateRelatedLookupSubmission} > */}
                <Modal.Body>

                    <Form.Row>
                        <>

                            <input type="hidden" name="module_id" id="module_id" value={Module_id} />
                            <select className="form-control selectbox" name="lookup_field" required id="selectbox" value={layout_role} onChange={(e)=> setPortal_layout_role(e.target.value) }>
                                <option value>--Select an option--</option>
                                {uerRoleLayoutdata.map((mvalue, mindex) => (
                                    (() => {
                                        return  <option value={mvalue.value} key={mindex} >{mvalue.label}</option>
                                    })()
                                ))}

                            </select>
                        </>
                    </Form.Row>

                </Modal.Body>

                <Modal.Footer className="fleex-wrap">
                    <Button type="button" className="btn btn-primary" onClick={(e) => { updateuerRoleLayout(e) }} >

                        <i className="mdi mdi-check"></i>
                                Update
                            </Button>
                    <Button variant="light m-2" onClick={() => { setUerRoleLayoutModal(false) }} > <i className="mdi mdi-close"></i> Cancel</Button>
                </Modal.Footer>
                {/* </Form> */}
            </Modal>
        </>





    );
};

function Dashboard() {
    return (
        <div>
            <BreadcrumbsCustom
                title="All Portal Clients"
                title_right="Home"
                title_right_two="Clients"
                from="master"
            />

            <div className="row">
                <div className="col-12">
                    <div className="card">
                        <div className="card-body">
                            {/* <h4 className="card-title">Task</h4> */}
                            <div className="row">
                                <div className="col-12">
                                    {/* Data table */}
                                    <div className="table-responsive">
                                        <AdvancedPaginationTable />
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default withRouter(Dashboard)
