import React, { useEffect, useMemo, useState } from 'react';
import { Form, Modal, Button } from 'react-bootstrap';
import DataTable from 'react-data-table-component';
import { Link, withRouter , useParams } from 'react-router-dom';
import BreadcrumbsCustom from '../../BreadcrumbsCustom';
import { MASTER_ADMIN_PUBLIC_URL } from '../../../constants';
import { AdminDashboard } from '../../../services/MasterServiceAdmin';
import cogoToast from 'cogo-toast';
import RowReorder from '../../RowReorder';

import BootstrapTable from 'react-bootstrap-table-next';
import paginationFactory from 'react-bootstrap-table2-paginator';
import ToolkitProvider, { Search } from 'react-bootstrap-table2-toolkit';
const { SearchBar } = Search;

function Dashboard() {
    const [data, setData] = useState([])
    const { module_id } = useParams()

    const columns = [
      {
        dataField: 'Account_Name',
        text: 'Account Name'
      },{
        dataField: 'Phone',
        text: 'Phone'
      },

     {
        dataField: 'module_id',
        text: 'Action',
        formatter: (row, key) => {
            return (
                <>
                    <Link className="btn btn-inverse-primary m-1" to={`${MASTER_ADMIN_PUBLIC_URL}/admin-contact-list/` + row}>
                        View Contacts
                    </Link>

                </>
            )
        }
     }

    ]



    const fetchData = async () => {
        AdminDashboard(module_id).then(res => {
            setData(res.accounts)
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

                                    <ToolkitProvider
                                        keyField="key"
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
