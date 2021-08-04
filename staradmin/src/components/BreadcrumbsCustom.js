import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import { PUBLIC_URL } from '../constants'

class BreadcrumbsCustom extends Component {
  
  render() {
    const home = this.props.from ? this.props.from : "";
    return (
      <div>
        <div className="page-header">
            <h3 className="page-title">
            { this.props.title }
            </h3>
            <nav aria-label="breadcrumb">
                <ol className="breadcrumb">
                    <li className="breadcrumb-item"><Link to={`${PUBLIC_URL}/`+home}> { this.props.title_right } </Link></li>
                    <li className="breadcrumb-item active" aria-current="page"> { this.props.title_right_two }  </li>
                </ol>
            </nav>
        </div>
      </div>

    )
  }
}

export default BreadcrumbsCustom
