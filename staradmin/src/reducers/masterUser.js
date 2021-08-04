
const initialState = {
    master_User: []
  }
  
  
  const masterUserReducer = (state = initialState, action) => {
  
      switch (action.type) {
        case "MASTER_ADMIN_NAME":
          return action.value;
          break;
        default:
          return state;
          
      }
    }
  
    export default masterUserReducer;