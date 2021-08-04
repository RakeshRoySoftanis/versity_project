
const initialState = {
  logged_contact: []
}


const user_contactReducer = (state = initialState, action) => {

    switch (action.type) {
      case "USER_ADMIN_NAME":
        return action.value;
        break;
      default:
        return state;
        
    }
  }

  export default user_contactReducer;