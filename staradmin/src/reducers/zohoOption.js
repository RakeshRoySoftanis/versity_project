
const initialState = {
  zoho_opton: []
  }
  
  
  const zoho = (state = initialState, action) => {
  
      switch (action.type) {
        case "ZH_OPTIONS":
          return action.value;
          break;
        default:
          return state;
          
      }
    }
  
    export default zoho;