
const initialState = {
  name: []
}


const commonReducer = (state = initialState, action) => {
    switch (action.type) {
      case "SITE_ADMIN_NAME":
        return action.value;
        break;

        case "SITE_ADMIN_NAME_TWO":
          return action.value;
          break;

      default:
        return state;
        
    }
  }

  export default commonReducer;