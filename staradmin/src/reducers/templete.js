
const initialState = {
    templete_Setting: []
  }
  
  
  const templete_Reducer = (state = initialState, action) => {
  
      switch (action.type) {
        case "TEMPLETE_SETTING":
          return action.value;
          break;
        default:
          return state;
          
      }
    }
  
    export default templete_Reducer;