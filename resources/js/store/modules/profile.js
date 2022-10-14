const state= {
    user:null,
    userStatus:null,
};

const getters= {
    user: state => {
        return state.user
    }
};

const actions= {
    fetchUser({commit,state},userId){
        commit('setUserStatus','Loading')
        axios
        .get("/api/users/" + userId)
        .then((res) => {
         commit('setUser',res.data)
         commit('setUserStatus','Success')
        })
        .catch((error) => {
            commit('setUserStatus','Error')
        })
    
    }
};

const mutations= {
    setUser(state,user){
        state.user = user;
    },
    setUserStatus(state,status){
        state.userStatus = status;
    }
};

export default {
    state,getters,actions,mutations,
}

