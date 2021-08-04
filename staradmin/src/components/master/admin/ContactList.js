import React, { useEffect, useMemo, useState } from 'react';
import { Form, Modal, Button } from 'react-bootstrap';
import DataTable from 'react-data-table-component';
import { Link, withRouter , useParams } from 'react-router-dom';
import BreadcrumbsCustom from '../../BreadcrumbsCustom';
import { MASTER_ADMIN_PUBLIC_URL } from '../../../constants';
import { showContactList , attemptLoggedIn } from '../../../services/MasterServiceAdmin';
import cogoToast from 'cogo-toast';

import BootstrapTable from 'react-bootstrap-table-next';
import paginationFactory from 'react-bootstrap-table2-paginator';
import ToolkitProvider, { Search } from 'react-bootstrap-table2-toolkit';
const { SearchBar } = Search;

function Dashboard() {
    const [data, setData] = useState([])
    const { module_id } = useParams()

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

    const columns = [
      {
        dataField: 'Full_Name',
        text: 'Name',
        sort: true
      },

      {
        dataField: 'Email',
        text: 'Email',
        sort: true
      },
      
      {
        dataField: 'Phone',
        text: 'Phone'
      },

     {
        dataField: 'module_id',
        text: 'Action',
        formatter: (cellContent , row, key) => {
            if(row.Portal_Status =="Active"){
               return <button type="button" className={row.Portal_Status =="Active" ? "btn btn-inverse-primary mr-2" : "btn btn-inverse-secoundery mr-2" }  onClick={() => loggedInPortal(cellContent)}><i className="mdi mdi-login "></i> Login</button>
            }
        }
     }

    ]

    const fetchData = async () => {
        showContactList(module_id).then(res => {
            setData(res.contacts)
        }).catch(err => err);
    }

    useEffect(() => {
        fetchData()
    }, [] )
    return (
        <div>

            <div className="row">
                <div className="col-12">
                    <div className="card">
                        <div className="card-body">
                            <div className="row">
                                <div className="col-12 moduleList ">

                                <h4 style={{ float: "right" }} className="float-right">
                                    <Link to={`${MASTER_ADMIN_PUBLIC_URL}/dashboard`} className="btn btn-primary float-right" > <i className="mdi mdi-keyboard-backspace"></i>  Back </Link>
                                </h4>

                                    <ToolkitProvider
                                        keyField="module_id"
                                        bootstrap4
                                        data={data}
                                        columns={columns}
                                        search
                                    >
                                        {
                                            props => (
                                                <div>
                                                    <div className="d-flex align-items-center">
                                                        <SearchBar { ...props.searchProps } />
                                                    </div>
                                                    <BootstrapTable
                                                        pagination={paginationFactory()}
                                                        {...props.baseProps}
                                                        wrapperClasses="table-responsive"
                                                    />
                                                </div>
                                            )
                                        }
                                    </ToolkitProvider>
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
