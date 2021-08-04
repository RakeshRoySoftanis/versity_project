import React, { Component } from 'react';
import { OverlayTrigger, Tooltip, Button, ButtonToolbar, Popover } from 'react-bootstrap';

export class Tooltips extends Component {
  render() {
    return (
      <div>
        <div className="page-header">
          <h3 className="page-title"> Tooltips </h3>
          <nav aria-label="breadcrumb">
            <ol className="breadcrumb">
              <li className="breadcrumb-item"><a href="!#" onClick={event => event.preventDefault()}>UI Elements</a></li>
              <li className="breadcrumb-item active" aria-current="page">Tooltips</li>
            </ol>
          </nav>
        </div>
        <div className="row">
          <div className="col-md-6 stretch-card">
            <div className="card">
              <div className="card-body">
                <h4 className="card-title">Basic tooltip</h4>
                <p className="card-description">Basic tooltip demo that appears on hover</p>
                <p>Hover the below button for interactive demo</p>
                <OverlayTrigger overlay={<Tooltip id="tooltip-disabled">Basic Tooltip</Tooltip>}>
                  <span className="d-inline-block">
                    <Button variant="primary" style={{ pointerEvents: 'none' }}>
                      Hover me
                    </Button>
                  </span>
                </OverlayTrigger>
              </div>
              <div className="card-body">
                <h4 className="card-title">Tooltip positions</h4>
                <p className="card-description">Add attribute <code>placement=&#123;position&#125;</code> to the element</p>
                <div className="tooltip-demo">
                <ButtonToolbar>
                  {['top', 'right', 'bottom', 'left'].map(placement => (
                    <OverlayTrigger
                      key={placement}
                      placement={placement}
                      overlay={
                        <Tooltip id={`tooltip-${placement}`}>
                          Tooltip on <strong>{placement}</strong>.
                        </Tooltip>
                      }
                    >
                      <Button variant="primary">Tooltip on {placement}</Button>
                    </OverlayTrigger>
                  ))}
                </ButtonToolbar>
                </div>
              </div>
              <div className="card-body">
                <h4 className="card-title">Tooltip Colors</h4>
                <p className="card-description">Add attribute <code>className=&#123;`tooltip-&#123;color&#125;`&#125;</code> to the element</p>
                <div className="tooltip-demo">
                <ButtonToolbar>
                  {['success', 'info', 'warning', 'primary', 'danger'].map(color => (
                    <OverlayTrigger
                      overlay={
                        <Tooltip className={`tooltip-${color}`} id={`tooltip-${color}`}>
                          Tooltip on <strong>{color}</strong>.
                        </Tooltip>
                      }
                    >
                      <Button variant={`${color}`}>Tooltip {color}</Button>
                    </OverlayTrigger>
                  ))}
                </ButtonToolbar>
                </div>
              </div>
            </div>
          </div>
          <div className="col-md-6 stretch-card">
            <div className="card">
              <div className="card-body">
                <h4 className="card-title">Basic popover</h4>
                <p className="card-description">Basic popover demo that appears on click</p>
                <p>Click the below button for interactive demo</p>
                <OverlayTrigger
                  trigger="click"
                  placement="right"
                  overlay={
                    <Popover id="popover-basic">
                      <Popover.Title as="h3">Popover title</Popover.Title>
                      <Popover.Content>
                        Sed posuere consectetur est at lobortis. Aenean eu leo quam.
                      </Popover.Content>
                    </Popover>
                  }
                >
                  <Button variant="primary">Click me</Button>
                </OverlayTrigger>
              </div>
              <div className="card-body">
                <h4 className="card-title">Popover positions</h4>
                <p className="card-description">Add attribute <code>placement="&#123;position&#125;"</code> to the element</p>
                <div className="popover-demo">
                  <ButtonToolbar>
                    {['top', 'right', 'bottom', 'left'].map(placement => (
                      <OverlayTrigger
                        trigger="click"
                        key={placement}
                        placement={placement}
                        overlay={
                          <Popover id={`popover-positioned-${placement}`}>
                            <Popover.Title as="h3">{`Popover ${placement}`}</Popover.Title>
                            <Popover.Content>
                              Sed posuere consectetur est at lobortis. Aenean eu leo quam.
                            </Popover.Content>
                          </Popover>
                        }
                      >
                        <Button variant="primary">Popover on {placement}</Button>
                      </OverlayTrigger>
                    ))}
                  </ButtonToolbar>
                </div>
              </div>
              <div className="card-body">
                <h4 className="card-title">Popover Colors</h4>
                <p className="card-description">Add attribute <code>placement="&#123;position&#125;"</code> to the element</p>
                <div className="popover-demo">
                  <ButtonToolbar>
                    {['success', 'info', 'warning', 'primary', 'danger'].map(color => (
                      <OverlayTrigger
                        trigger="click"
                        overlay={
                          <Popover id={`popover-color-${color}`} className={`popover-${color}`}>
                            <Popover.Title as="h3">{`Popover ${color}`}</Popover.Title>
                            <Popover.Content>
                              Sed posuere consectetur est at lobortis. Aenean eu leo quam.
                            </Popover.Content>
                          </Popover>
                        }
                      >
                        <Button variant={`${color}`}>Popover on {color}</Button>
                      </OverlayTrigger>
                    ))}
                  </ButtonToolbar>
                </div>
              </div>
            </div>
          </div>
          {/* <div className="col-12">
            <div className="card">
              <div className="card-body">
                <h4 className="card-title">Popover Colors</h4>
                <p className="card-description">Add attribute <code>data-custom-className="popover-{color}"</code> to the element</p>
                <div className="popover-static-demo">
                  <div className="popover bs-popover-bottom bs-popover-bottom-demo popover-success">
                    <div className="arrow"></div>
                    <h3 className="popover-header">Success</h3>
                    <div className="popover-body">
                      <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam.</p>
                    </div>
                  </div>
                  <div className="popover bs-popover-bottom bs-popover-bottom-demo popover-warning">
                    <div className="arrow"></div>
                    <h3 className="popover-header">Warning</h3>
                    <div className="popover-body">
                      <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam.</p>
                    </div>
                  </div>
                  <div className="popover bs-popover-bottom bs-popover-bottom-demo popover-danger">
                    <div className="arrow"></div>
                    <h3 className="popover-header">Danger</h3>
                    <div className="popover-body">
                      <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam.</p>
                    </div>
                  </div>
                  <div className="popover bs-popover-bottom bs-popover-bottom-demo popover-info">
                    <div className="arrow"></div>
                    <h3 className="popover-header">Info</h3>
                    <div className="popover-body">
                      <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam.</p>
                    </div>
                  </div>
                  <div className="popover bs-popover-bottom bs-popover-bottom-demo popover-primary">
                    <div className="arrow"></div>
                    <h3 className="popover-header">Primary</h3>
                    <div className="popover-body">
                      <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam.</p>
                    </div>
                  </div>
                  <div className="clearfix"></div>
                </div>
              </div>
            </div>
          </div> */}
        </div>
      </div>
    )
  }
}

export default Tooltips
