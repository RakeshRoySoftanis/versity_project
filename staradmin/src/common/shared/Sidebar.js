import React, { Component } from 'react';
import { Link, useParams, withRouter, useHistory } from 'react-router-dom';

import { Collapse } from 'react-bootstrap';
import { PUBLIC_URL } from '../../constants';
import { connect } from 'react-redux';

class Sidebars extends Component {
  state = {
    otherBool: false
  };

  toggleMenuState(menuState) {
    if (this.state[menuState]) {
      this.setState({ [menuState]: false });
    } else if (Object.keys(this.state).length === 0) {
      this.setState({ [menuState]: true });
    } else {
      Object.keys(this.state).forEach(i => {
        this.setState({ [i]: false });
      });
      this.setState({ [menuState]: true });
    }
  }

  toggleMenuState(menuState) {
    if (this.state[menuState]) {
      this.setState({ [menuState]: false });
    } else if (Object.keys(this.state).length === 0) {
      this.setState({ [menuState]: true });
    } else {
      Object.keys(this.state).forEach(i => {
        this.setState({ [i]: false });
      });
      this.setState({ [menuState]: true });
    }
  }

  componentDidUpdate(prevProps) {
    if (this.props.location !== prevProps.location) {
      this.onRouteChanged();
    }
  }


  onRouteChanged() {

    if (parseInt(this.props.templete.id) === 1 || parseInt(this.props.templete.id) === 2) {
      document.querySelector('#sidebar').classList.remove('active');
      Object.keys(this.state).forEach(i => {
        this.setState({ [i]: false });
      });
    }

    // const dropdownPaths = [
    //   {path:'/apps', state: 'appsMenuOpen'},
    //   {path:'/basic-ui', state: 'basicUiMenuOpen'},
    //   {path:'/advanced-ui', state: 'advancedUiMenuOpen'},
    //   {path:'/form-elements', state: 'formElementsMenuOpen'},
    //   {path:'/tables', state: 'tablesMenuOpen'},
    //   {path:'/maps', state: 'mapsMenuOpen'},
    //   {path:'/icons', state: 'iconsMenuOpen'},
    //   {path:'/charts', state: 'chartsMenuOpen'},
    //   {path:'/user-pages', state: 'userPagesMenuOpen'},
    //   {path:'/error-pages', state: 'errorPagesMenuOpen'},
    //   {path:'/general-pages', state: 'generalPagesMenuOpen'},
    //   {path:'/ecommerce', state: 'ecommercePagesMenuOpen'},
    // ];

    // dropdownPaths.forEach((obj => {
    //   if (this.isPathActive(obj.path)) {
    //     this.setState({[obj.state] : true})
    //   }
    // }));

  }





  render() {
    let Zh_subscription = this.props.setting.want_zsubscriptions
    let Zh_project = this.props.setting.want_zprojects
    let Zh_desk = this.props.setting.want_zdesks
    let Zh_inventory = this.props.setting.want_inventory
    let Zh_vault = this.props.setting.want_zvaults
    let Zh_workdrive = this.props.setting.want_zworkdrive
    let zohoOpton_subscription = this.props.zoho.Subscription
    let zohoOpton_inventory = this.props.zoho.inventory
    let zh_subs_products;
    let zh_subs_subs;
    let zh_inventory_inventory

    const dropdownAction = (e, rownum1) => {
      let defel = document.getElementById("drop_option")
      defel.classList.add("disable-color")

      if (this.state.otherBool == false) this.setState({ otherBool: true })
      if (this.state.otherBool == true) this.setState({ otherBool: false })
    }

    if (zohoOpton_subscription != undefined) {
      zh_subs_products = zohoOpton_subscription.products;
      zh_subs_subs = zohoOpton_subscription.subscriptions;
    }

    if (zohoOpton_inventory != undefined) {
      zh_inventory_inventory = zohoOpton_inventory.inventory;
    }

    var zh_project_integration, zh_desk_integration, zh_vault_integration, zh_workDrive_integration;

    if (this.props.zoho.desk != undefined) {
      zh_desk_integration = this.props.zoho.desk.tickets;
    }

    if (this.props.zoho.project != undefined) {
      zh_project_integration = this.props.zoho.project.tasks;
    }

    if (this.props.zoho.vault != undefined) {
      zh_vault_integration = this.props.zoho.vault.chambers;
    }

    if (this.props.zoho.workDrive != undefined) {
      zh_workDrive_integration = this.props.zoho.workDrive.work_drive;
    }

    return (
      <>

        <li className={this.isPathActive('/dashboard') ? 'nav-item active home-mt' : 'nav-item home-mt'}>
          <Link className="nav-link" to={`${PUBLIC_URL}/dashboard`}>
            <i className="mdi mdi-television menu-icon"></i>
            <span className="menu-title">Home</span>
          </Link>
        </li>

        <li className={this.isPathActive('/my-profile') ? 'nav-item active' : 'nav-item'}>
          <Link className="nav-link" to={`${PUBLIC_URL}/my-profile`}>
            <i className="mdi mdi-account menu-icon"></i>
            <span className="menu-title">Profile</span>
          </Link>
        </li>
        {(() => {
          if ((this.props.templete != undefined && this.props.templete.template_api_name == "template_one") || (this.props.templete != undefined && this.props.templete.template_api_name == "template_two")) {
            return (
              <>
                {
                  this.props.module_listName.map((module, key) =>
                    (() => {
                      if ((module.name != 'Tasks') && (module.name != 'Events') && (module.name != 'Calls')) {
                        return (
                          <li key={key} className={this.isPathActive("/module-list/" + module.name) || this.isPathActive("/module-add/" + module.name) || this.isPathActive("/module-edit/" + module.name) || this.isPathActive("/create-view/" + module.name) || this.isPathActive("/module-details/" + module.name) || this.isPathActive("/" + module.name + "/edit-view") ? 'nav-item active' : 'nav-item'}>
                            <Link className="nav-link" to={`${PUBLIC_URL}/${module.url}`}>
                              <i className="mdi mdi-arrange-send-backward menu-icon"></i>
                              <span className="menu-title">{module.display_name}</span>
                            </Link>
                          </li>
                        )
                      }
                    })()
                  )
                }
                {/* {
                  this.props.hasActivitis == true ?
                    <li className={this.isPathActive("/module-list/Tasks") || this.isPathActive("/module-list/Events") || this.isPathActive("/module-list/Calls") || this.isPathActive("/module-add/Tasks") || this.isPathActive("/module-edit/Tasks") || this.isPathActive("/module-add/Calls") || this.isPathActive("/module-edit/Calls") || this.isPathActive("/module-add/Events") || this.isPathActive("/module-edit/Events") ? 'nav-item active' : 'nav-item'}>
                      <div className={this.state.AllActivitis ? 'nav-link menu-expanded' : 'nav-link'} onClick={() => this.toggleMenuState('AllActivitis')} data-toggle="collapse">
                        <i className="mdi mdi-arrange-send-backward menu-icon"></i>
                        <span className="menu-title">Activities </span>
                        <i className="menu-arrow"></i>
                      </div>
                      <Collapse in={this.state.AllActivitis}>
                        <ul className="nav flex-column sub-menu">
                         
                          <li className="nav-item sub-item"> <Link className={this.isPathActive("/module-list/Activities") ? 'nav-link sub-link active' : 'nav-link'} to={`${PUBLIC_URL}/module-list/Activities`}>All Activities </Link></li>
                        </ul>
                      </Collapse>
                    </li> :
                    ""
                } */}


                <li className={(this.isPathActive('/zc-contacts') || this.isPathActive('/contact-details') || this.isPathActive('/store-contacts')) ? 'nav-item active' : 'nav-item'}>
                  <Link className="nav-link" to={`${PUBLIC_URL}/zc-contacts`}>
                    <i className="mdi mdi-account-multiple menu-icon"></i>
                    <span className="menu-title">Contacts</span>
                  </Link>
                </li>
                <li className={(this.isPathActive('/task') || this.isPathActive('/task-detail')) ? 'nav-item active' : 'nav-item'}>
                  <Link className="nav-link" to={`${PUBLIC_URL}/task`}>
                    <i className="fa fa-tasks menu-icon"></i>
                    <span className="menu-title">Tasks</span>
                  </Link>
                </li>


                <li className={this.isPathActive('/my-projects') || this.isPathActive('/my-project-detail') ? 'nav-item active' : 'nav-item'}>
                  <Link className="nav-link" to={`${PUBLIC_URL}/my-projects`}>
                    <i className="fa fa-list-alt menu-icon"></i>
                    <span className="menu-title">My Projects</span>
                  </Link>
                </li>

                {/* zoho integration portal menus start */}

                {/* zoho books  */}
                <li className={(this.isPathActive('/zb-invoices') || this.isPathActive('/zb-invoice-details')) ? 'nav-item active' : 'nav-item'}>
                  <Link className="nav-link" to={`${PUBLIC_URL}/zb-invoices`}>
                    <i className="fa fa-dollar menu-icon"></i>
                    <span className="menu-title">Invoices</span>
                  </Link>
                </li>

                {/* zoho Projects  */}
                {
                  (Zh_project == "Yes" && zh_project_integration == "on") ?
                    (
                      <li className={(this.isPathActive('/zp-projects') || this.isPathActive('/zp-milestones')) ? 'nav-item active' : 'nav-item'}>
                        <Link className="nav-link" to={`${PUBLIC_URL}/zp-projects`}>
                          <i className="fa fa-list-alt menu-icon"></i>
                          <span className="menu-title">Projects</span>
                        </Link>
                      </li>
                    ) : ("")
                }


                {/* zoho Desk  */}

                {
                  (Zh_desk == "Yes" && zh_desk_integration == "on") ?
                    (
                      <li className={(this.isPathActive('/zd-tickets') || this.isPathActive('/zd-ticket-details')) ? 'nav-item active' : 'nav-item'}>
                        <Link className="nav-link" to={`${PUBLIC_URL}/zd-tickets`}>
                          <i className="fa fa-ticket menu-icon"></i>
                          <span className="menu-title">Tickets</span>
                        </Link>
                      </li>
                    ) : ("")
                }

                {
                  (Zh_vault == "Yes" && zh_vault_integration == "on") ?
                    (
                      <li className={(this.isPathActive('/my-zv-chambers')) || (this.isPathActive('/zv-add-secret')) || (this.isPathActive('/zv-edit-secret')) ? 'nav-item active' : 'nav-item'}>
                        <Link className="nav-link" to={`${PUBLIC_URL}/my-zv-chambers`}>
                          <i className="mdi mdi-lock-open-outline menu-icon"></i>
                          <span className="menu-title">Vault</span>
                        </Link>
                      </li>
                    ) : ("")
                }

                {/* zoho inventory  */}

                {
                  (Zh_inventory !== "No") ?

                    (this.props.templete.id == 1 || this.props.templete.id == 2 || window.innerWidth <= 768) ?  // Templete 1 and 2
                      (
                        <li className={this.isPathActive('/zi-items') || this.isPathActive('/zi-invoices') || this.isPathActive('/zi-item-details') || this.isPathActive('/zi-invoice-details') ? 'nav-item active' : 'nav-item'}>
                          <div className={this.state.inventory ? 'nav-link menu-expanded' : 'nav-link'} onClick={() => this.toggleMenuState('inventory')} data-toggle="collapse">
                            <i className="mdi mdi-cart-outline menu-icon"></i>
                            <span className="menu-title">Inventory </span>
                            <i className="menu-arrow"></i>
                          </div>
                          <Collapse in={this.state.inventory}>
                            <ul className="nav flex-column sub-menu">
                              <li className="nav-item sub-item"> <Link className={this.isPathActive('/zi-items') || this.isPathActive('/zi-item-details') ? 'nav-link active' : 'nav-link'} to={PUBLIC_URL + "/zi-items"}>Products</Link></li>
                              <li className="nav-item sub-item"> <Link className={this.isPathActive('/zi-invoices') || this.isPathActive('/zi-invoice-details') ? 'nav-link active' : 'nav-link'} to={PUBLIC_URL + "/zi-invoices"}> Invoices </Link></li>
                            </ul>
                          </Collapse>
                        </li>
                      ) : (
                        <li className={this.isPathActive('/zi-items') || this.isPathActive('/zi-invoices') || this.isPathActive('/zi-item-details') || this.isPathActive('/zi-invoice-details') ? 'nav-item active' : 'nav-item'}>
                          <div className={this.state.inventory ? 'nav-link menu-expanded' : 'nav-link'} onClick={() => this.toggleMenuState('inventory')} onMouseEnter={() => this.toggleMenuState('inventory')} >
                            <i className="mdi mdi-cart-outline menu-icon"></i>
                            <span className="menu-title">Inventory </span>
                            <i className="menu-arrow"></i>
                          </div>

                          <div className="dropdown" onMouseLeave={() => this.toggleMenuState('inventory')} >
                            {
                              this.state.inventory ? (
                                <div className="dropdown-menu show" aria-labelledby="dropdownMenuButton">
                                  <ul className="nav flex-column sub-menu submenu-item">
                                    <li className="nav-item sub-item"> <Link className={this.isPathActive('/zi-items') || this.isPathActive('/zi-item-details') ? 'nav-link active' : 'nav-link'} to={PUBLIC_URL + "/zi-items"}>Products</Link></li>
                                    <li className="nav-item sub-item"> <Link className={this.isPathActive('/zi-invoices') || this.isPathActive('/zi-invoice-details') ? 'nav-link active' : 'nav-link'} to={PUBLIC_URL + "/zi-invoices"}> Invoices </Link></li>
                                  </ul>
                                </div>
                              ) : (
                                <div className="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                  <ul className="nav flex-column sub-menu submenu-item">
                                    <li className="nav-item sub-item"> <Link className={this.isPathActive('/zi-items') || this.isPathActive('/zi-item-details') ? 'nav-link active' : 'nav-link'} to={PUBLIC_URL + "/zi-items"}>Products</Link></li>
                                    <li className="nav-item sub-item"> <Link className={this.isPathActive('/zi-invoices') || this.isPathActive('/zi-invoice-details') ? 'nav-link active' : 'nav-link'} to={PUBLIC_URL + "/zi-invoices"}> Invoices </Link></li>
                                  </ul>
                                </div>
                              )
                            }
                          </div>
                        </li>
                      )

                    : ""
                }

                {
                  (Zh_workdrive == "Yes" && zh_workDrive_integration == "on") ?
                    (
                      <li className={this.isPathActive('/zwd-files') || this.isPathActive('/zwd-folder') ? 'nav-item active' : 'nav-item'}>
                        <Link className="nav-link" to={`${PUBLIC_URL}/zwd-files`}>
                          <i className="mdi mdi-folder menu-icon"></i>
                          <span className="menu-title">Work Drive</span>
                        </Link>
                      </li>
                    ) : ("")
                }
              </>
            )
          }
        })()}



        {(() => {
          if ((this.props.templete != undefined && this.props.templete.template_api_name == "template_four") || (this.props.templete != undefined && this.props.templete.template_api_name == "template_three")) {
            return (
              <>
                {
                  this.props.module_listName.map((module, key) =>

                    (() => {
                      if ((module.name != 'Tasks') && (module.name != 'Events') && (module.name != 'Calls') && key < 6) {

                        return (
                          <li key={key} className={this.isPathActive("/module-list/" + module.name) || this.isPathActive("/create-view/" + module.name) || this.isPathActive("/module-details/" + module.name) || this.isPathActive("/" + module.name + "/edit-view") ? 'nav-item active' : 'nav-item'}>
                            <Link className="nav-link" to={`${PUBLIC_URL}/${module.url}`}>
                              <i className="mdi mdi-arrange-send-backward menu-icon"></i>
                              <span className="menu-title">{module.display_name}</span>
                            </Link>
                          </li>
                        )
                      }
                    })()
                  )
                }

                {/* {
                  (window.innerWidth <= 768 && this.props.hasActivitis == true) ?  // Templete 1 and 2
                    (
                      <li className={this.isPathActive("/module-list/task") || this.isPathActive("/module-list/Events") || this.isPathActive("/module-list/Calls") ? 'nav-item active' : 'nav-item'}>
                        <div className={this.state.AllActivitis ? 'nav-link menu-expanded' : 'nav-link'} onClick={() => this.toggleMenuState('AllActivitis')} onMouseEnter={() => this.toggleMenuState('AllActivitis')}  >
                          <i className="mdi mdi-arrange-send-backward menu-icon"></i>
                          <span className="menu-title">Activities  </span>
                        </div>

                        <Collapse in={this.state.AllActivitis}>

                          <ul className="nav flex-column sub-menu">
                            {
                              this.props.module_listName.map((module, key) =>

                                (() => {
                                  if (module.name == 'Tasks' || module.name == 'Events' || module.name == 'Calls') {

                                    return (
                                      <li className="nav-item"> <Link className={this.isPathActive("/module-list/" + module.name) ? 'nav-link active' : 'nav-link'} to={`${PUBLIC_URL}/${module.url}`}>All  {module.name}</Link></li>
                                    )
                                  }
                                })()
                              )
                            }
                            <li className="nav-item sub-item"> <Link className={this.isPathActive("/module-list/Activities") ? 'nav-link sub-link active' : 'nav-link'} to={`${PUBLIC_URL}/module-list/Activities`}>All Activities </Link></li>
                          </ul>
                        </Collapse>
                      </li>
                    ) : (
                      this.props.hasActivitis == true ?
                        <li className={this.isPathActive("/module-list/task") || this.isPathActive("/module-list/Events") || this.isPathActive("/module-list/Calls") ? 'nav-item active' : 'nav-item'}>
                          <div className={this.state.AllActivitis ? 'nav-link menu-expanded' : 'nav-link'} onClick={() => this.toggleMenuState('AllActivitis')} onMouseEnter={() => this.toggleMenuState('AllActivitis')}  >
                            <i className="mdi mdi-arrange-send-backward menu-icon"></i>
                            <span className="menu-title">Activities  </span>
                          </div>
                          <div className="dropdown" onMouseLeave={() => this.toggleMenuState('AllActivitis')} >
                            {
                              this.state.AllActivitis ? (<div className="dropdown-menu show" aria-labelledby="dropdownMenuButton">
                                <ul className="nav flex-column sub-menu">
                                  {
                                    this.props.module_listName.map((module, key) =>

                                      (() => {
                                        if (module.name == 'Tasks' || module.name == 'Events' || module.name == 'Calls') {

                                          return (
                                            <li className="nav-item"> <Link className={this.isPathActive("/module-list/" + module.name) ? 'nav-link active' : 'nav-link'} to={`${PUBLIC_URL}/${module.url}`}>All  {module.name}</Link></li>
                                          )
                                        }
                                      })()
                                    )
                                  }
                                </ul>
                              </div>) : (

                                <div className="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                </div>
                              )
                            }

                          </div>
                        </li>
                        : ""
                    )
                } */}

                {/* zoho subscriptions  */}

                {
                  (Zh_subscription == "Yes") ?
                    (this.props.templete.id == 1 || this.props.templete.id == 2 || window.innerWidth <= 768) ?  // Templete 1 and 2
                      (<li className={this.isPathActive('/zs-products') || this.isPathActive('/zs-plans') || this.isPathActive('/zs-subscriptions') ? 'nav-item active' : 'nav-item'}>
                        <div className={this.state.zsMenuOpen ? 'nav-link menu-expanded' : 'nav-link'} onClick={() => this.toggleMenuState('zsMenuOpen')} data-toggle="collapse">
                          <i className="mdi mdi-arrange-send-backward menu-icon"></i>
                          <span className="menu-title">Subscriptions </span>
                        </div>
                        <Collapse in={this.state.zsMenuOpen}>
                          <ul className="nav flex-column sub-menu">
                            {
                              (zh_subs_products == "on") ?
                                (<li className="nav-item"> <Link className={this.isPathActive('/zs-products') || this.isPathActive('/zs-plans') ? 'nav-link active' : 'nav-link'} to={`${PUBLIC_URL}/zs-products`}>Products</Link></li>) : ""
                            }
                            {
                              (zh_subs_subs == "on") ?
                                (<li className="nav-item"> <Link className={this.isPathActive('/zs-subscriptions') ? 'nav-link active' : 'nav-link'} to={`${PUBLIC_URL}/zs-subscriptions`}> My Subscription </Link></li>) : ""
                            }
                          </ul>
                        </Collapse>
                      </li>
                      ) : (
                        <li className={this.isPathActive('/zs-products') || this.isPathActive('/zs-plans') || this.isPathActive('/zs-subscriptions') ? 'nav-item active' : 'nav-item'}>
                          <div className={this.state.zsMenuOpen ? 'nav-link menu-expanded' : 'nav-link'} onClick={() => this.toggleMenuState('zsMenuOpen')} onMouseEnter={() => this.toggleMenuState('zsMenuOpen')}  >
                            <i className="mdi mdi-arrange-send-backward menu-icon"></i>
                            <span className="menu-title">Subscriptions  </span>
                          </div>

                          <div className="dropdown" onMouseLeave={() => this.toggleMenuState('zsMenuOpen')} >
                            {
                              this.state.zsMenuOpen ? (<div className="dropdown-menu show" aria-labelledby="dropdownMenuButton">
                                <ul className="nav flex-column sub-menu submenu-item">
                                  {
                                    (zh_subs_products == "on") ?
                                      (<li className="nav-item"> <Link className={this.isPathActive('/zs-products') || this.isPathActive('/zs-plans') ? 'nav-link active' : 'nav-link'} to={`${PUBLIC_URL}/zs-products`}>Products</Link></li>) : ""
                                  }
                                  {
                                    (zh_subs_subs == "on") ?
                                      (<li className="nav-item"> <Link className={this.isPathActive('/zs-subscriptions') ? 'nav-link active' : 'nav-link'} to={`${PUBLIC_URL}/zs-subscriptions`}> My Subscription </Link></li>) : ""
                                  }
                                </ul>
                              </div>) : (
                                <div className="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                  <ul className="nav flex-column sub-menu submenu-item">
                                    {
                                      (zh_subs_products == "on") ?
                                        (<li className="nav-item"> <Link className={this.isPathActive('/zs-products') || this.isPathActive('/zs-plans') ? 'nav-link active' : 'nav-link'} to={`${PUBLIC_URL}/zs-products`}>Products</Link></li>) : ""
                                    }
                                    {
                                      (zh_subs_subs == "on") ?
                                        (<li className="nav-item"> <Link className={this.isPathActive('/zs-subscriptions') ? 'nav-link active' : 'nav-link'} to={`${PUBLIC_URL}/zs-subscriptions`}> My Subscription </Link></li>) : ""
                                    }
                                  </ul>
                                </div>
                              )
                            }

                          </div>
                        </li>
                      )

                    : ""
                }

                {
                  (Zh_inventory !== "No") ?

                    (this.props.templete.id == 1 || this.props.templete.id == 2 || window.innerWidth <= 768) ?
                      (
                        <li className={this.isPathActive('/zi-items') || this.isPathActive('/zi-invoices') || this.isPathActive('/zi-item-details') || this.isPathActive('/zi-invoice-details') ? 'nav-item active' : 'nav-item'}>
                          <div className={this.state.inventory ? 'nav-link menu-expanded' : 'nav-link'} onClick={() => this.toggleMenuState('inventory')} data-toggle="collapse">
                            <i className="mdi mdi-cart-outline menu-icon"></i>
                            <span className="menu-title">Inventory </span>
                            <i className="menu-arrow"></i>
                          </div>
                          <Collapse in={this.state.inventory}>
                            <ul className="nav flex-column sub-menu">
                              <li className="list-label sub-item"> <Link className={this.isPathActive('/zi-items') || this.isPathActive('/zi-item-details') ? ' active' : ''} to={PUBLIC_URL + "/zi-items"}>Products</Link></li>
                              <li className="list-label"> <Link className={this.isPathActive('/zi-invoices') || this.isPathActive('/zi-invoice-details') ? ' sub-link active' : ''} to={PUBLIC_URL + "/zi-invoices"}> Invoices </Link></li>
                            </ul>
                          </Collapse>
                        </li>
                      ) : (
                        <li className={this.isPathActive('/zi-items') || this.isPathActive('/zi-invoices') || this.isPathActive('/zi-item-details') || this.isPathActive('/zi-invoice-details') ? 'nav-item active' : 'nav-item'}>
                          <div className={this.state.inventory ? 'nav-link menu-expanded' : 'nav-link'} onClick={() => this.toggleMenuState('inventory')} onMouseEnter={() => this.toggleMenuState('inventory')} >
                            <i className="mdi mdi-cart-outline menu-icon"></i>
                            <span className="menu-title">Inventory </span>
                            <i className="menu-arrow"></i>
                          </div>

                          <div className="dropdown" onMouseLeave={() => this.toggleMenuState('inventory')} >
                            {
                              this.state.inventory ? (
                                <div className="dropdown-menu show" aria-labelledby="dropdownMenuButton">
                                  <ul className="nav flex-column sub-menu submenu-item">
                                    <li className="nav-item"> <Link className={this.isPathActive('/zi-items') || this.isPathActive('/zi-item-details') ? 'nav-link active' : 'nav-link'} to={PUBLIC_URL + "/zi-items"}>Products</Link></li>
                                    <li className="nav-item"> <Link className={this.isPathActive('/zi-invoices') || this.isPathActive('/zi-invoice-details') ? 'nav-link active' : 'nav-link'} to={PUBLIC_URL + "/zi-invoices"}> Invoices </Link></li>
                                  </ul>
                                </div>
                              ) : (
                                <div className="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                  <ul className="nav flex-column sub-menu submenu-item">
                                    <li className="nav-item"> <Link className={this.isPathActive('/zi-items') || this.isPathActive('/zi-item-details') ? 'nav-link active' : 'nav-link'} to={PUBLIC_URL + "/zi-items"}>Products</Link></li>
                                    <li className="nav-item"> <Link className={this.isPathActive('/zi-invoices') || this.isPathActive('/zi-invoice-details') ? 'nav-link active' : 'nav-link'} to={PUBLIC_URL + "/zi-invoices"}> Invoices </Link></li>
                                  </ul>
                                </div>
                              )
                            }
                          </div>
                        </li>
                      )
                    : ""
                }

                <li className={(this.isPathActive('/zc-contacts') || this.isPathActive('/contact-details') || this.isPathActive('/store-contacts')) ? 'nav-item ' : 'nav-item'}>

                  <div className={this.state.AllActivitis ? 'nav-link ' : 'nav-link'} id="drop_option" onClick={(e) => { dropdownAction(e) }} >

                    <i className="fa fa-ellipsis-h i-icon" aria-hidden="true" style={{ fontSize: '18px', color: '#fff', marginTop: '5px', marginLeft: '1px' }} />
                    <i className="menu-arrow"></i>
                  </div>

                  <div className={this.state.otherBool == true ? "other-option-list" : "other-option-list div-hide"}   >
                    <div className="list-label-wrap ">
                      {
                        this.props.module_listName.map((module, key) =>

                          (() => {
                            if ((module.name != 'Tasks') && (module.name != 'Events') && (module.name != 'Calls') && key > 5) {

                              return (
                                <li key={key} className={this.isPathActive("/module-list/" + module.name) || this.isPathActive("/create-view/" + module.name) || this.isPathActive("/module-details/" + module.name) || this.isPathActive("/" + module.name + "/edit-view") ? 'list-label  active' : 'list-label '}>
                                  <Link className="" to={`${PUBLIC_URL}/${module.url}`}>
                                    <i className="mdi mdi-arrange-send-backward menu-icon"></i>
                                    <span className="menu-title">{module.display_name}</span>
                                  </Link>
                                </li>
                              )
                            }
                          })()
                        )

                      }


                      {/* <li className={(this.isPathActive('/zc-contacts') || this.isPathActive('/contact-details') || this.isPathActive('/store-contacts')) ? 'list-label active' : 'list-label'}>
                        <Link className="" to={`${PUBLIC_URL}/zc-contacts`}>
                          <i className="mdi mdi-account-multiple menu-icon"></i>
                          <span className="menu-title">Contacts</span>
                        </Link>
                      </li> */}
                      <li className={(this.isPathActive('/task') || this.isPathActive('/task-detail')) ? 'list-label active' : 'list-label'}>
                        <Link className="" to={`${PUBLIC_URL}/task`}>
                          <i className="fa fa-tasks menu-icon"></i>
                          <span className="menu-title">Tasks</span>
                        </Link>
                      </li>


                      <li className={this.isPathActive('/my-projects') || this.isPathActive('/my-project-detail') ? 'list-label active' : 'list-label'}>
                        <Link className="" to={`${PUBLIC_URL}/my-projects`}>
                          <i className="fa fa-list-alt menu-icon"></i>
                          <span className="menu-title">My Projects</span>
                        </Link>
                      </li>
                      <li className={(this.isPathActive('/zb-invoices') || this.isPathActive('/zb-invoice-details')) ? 'list-label active' : 'list-label'}>
                        <Link className="" to={`${PUBLIC_URL}/zb-invoices`}>
                          <i className="fa fa-dollar menu-icon inv-mrg" ></i>
                          <span className="menu-title">Invoices</span>
                        </Link>
                      </li>

                      {
                        (Zh_project == "Yes" && zh_project_integration == "on") ?
                          (
                            <li className={(this.isPathActive('/zp-projects') || this.isPathActive('/zp-milestones')) ? 'list-label active' : 'list-label'}>
                              <Link className="" to={`${PUBLIC_URL}/zp-projects`}>
                                <i className="fa fa-list-alt menu-icon"></i>
                                <span className="menu-title">Projects</span>
                              </Link>
                            </li>
                          ) : ("")
                      }


                      {/* zoho Desk  */}

                      {
                        (Zh_desk == "Yes" && zh_desk_integration == "on") ?
                          (
                            <li className={(this.isPathActive('/zd-tickets') || this.isPathActive('/zd-ticket-details')) ? 'list-label active' : 'list-label'}>
                              <Link className="" to={`${PUBLIC_URL}/zd-tickets`}>
                                <i className="fa fa-ticket menu-icon"></i>
                                <span className="menu-title">Tickets</span>
                              </Link>
                            </li>
                          ) : ("")
                      }

                      {
                        (Zh_vault == "Yes" && zh_vault_integration == "on") ?
                          (
                            <li className={(this.isPathActive('/my-zv-chambers')) || (this.isPathActive('/zv-add-secret')) || (this.isPathActive('/zv-edit-secret')) ? 'list-label active' : 'list-label'}>
                              <Link className="" to={`${PUBLIC_URL}/my-zv-chambers`}>
                                <i className="mdi mdi-lock-open-outline menu-icon"></i>
                                <span className="menu-title">Vault</span>
                              </Link>
                            </li>
                          ) : ("")
                      }

                      {
                        (Zh_workdrive == "Yes" && zh_workDrive_integration == "on") ?
                          (
                            <li className={this.isPathActive('/zwd-files') || this.isPathActive('/zwd-folder') ? 'list-label active' : 'list-label'}>
                              <Link className="" to={`${PUBLIC_URL}/zwd-files`}>
                                <i className="mdi mdi-folder menu-icon"></i>
                                <span className="menu-title">Work Drive</span>
                              </Link>
                            </li>
                          ) : ("")
                      }


                    </div>



                  </div>

                </li>
              </>
            )
          }
        })()}





      </>

    );

  }

  isPathActive(path) {
    return this.props.location.pathname.startsWith(path);
  }

  // componentDidMount() {
  //   this.onRouteChanged();
  //   // add className 'hover-open' to sidebar navitem while hover in sidebar-icon-only menu

  //   console.log("xxxxx " + this.props.templete.id)
  //   const body = document.querySelector('body');
  //   document.querySelectorAll('.sidebar .nav-item').forEach((el) => {

  //     el.addEventListener('mouseover', function() {
  //       if(body.classList.contains('sidebar-icon-only')) {
  //         el.classList.add('hover-open');
  //       }
  //     });
  //     el.addEventListener('mouseout', function() {
  //       if(body.classList.contains('sidebar-icon-only')) {
  //         el.classList.remove('hover-open');
  //       }
  //     });
  //   });
  // }

}


// const getTheArray = (val) => {
//   return {
//     type: 'INCREMENT',
//     value: val
//   }
// }

// let mapDispatchToProps = (dispatch) => {
//   return {
//     dispatchName: (array) => dispatch( getTheArray(array) ),
//   };
// };

let mapStateToProps = (state) => {

  return {
    isLogged: state.isLogged,
    user: state.user_contact,
    logged_contact: state.logged_contact,
    templete: state.templete,
    setting: state.setting,
    zoho: state.zoho
  };
};

const Sidebar = connect(
  mapStateToProps
  // mapDispatchToProps
)(Sidebars);


export default withRouter(Sidebar);