
const initialState = {
  SETTING: []
  }
  
  
  const setting = (state = initialState, action) => {
  
      switch (action.type) {
        case "SETTING":
          return action.value;
          break;
        default:
          return state;
          
      }
    }
  
    export default setting;