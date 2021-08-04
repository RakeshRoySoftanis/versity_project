
const initialState = {
  logged_contact: []
}


const logged_contactReducer = (state = initialState, action) => {
    switch (action.type) {
      case "LOGGED_ADMIN_NAME":
        return action.value;
        break;
      default:
        return state;
        
    }
  }

  export default logged_contactReducer;