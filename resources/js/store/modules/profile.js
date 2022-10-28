const state = {
    user: null,
    userStatus: null,

};

const getters = {
    user: state => {
        return state.user
    },
    status: state => {
        return {
            user: state.userStatus,
            posts: state.postsStatus,
        }
    },
    friendship: state => {
        return state.user.data.attributes.friendship
    },
    FriendButtontext: (state, getters, rootState) => {

        if(rootState.User.user.data.user_id === state.user.data.user_id){
            return '';
        }
        if (getters.friendship === null) {
            return 'Add Friend';
        } else if (getters.friendship.data.attributes.confirmed_at === null
            && getters.friendship.data.attributes.friend_id !== rootState.User.user.data.user_id) {
            return 'Pending Friend Request ';
        } else if (getters.friendship.data.attributes.confirmed_at !== null) {
            return '';
        }

        return 'Accept';

    }
};

const actions = {
    fetchUser({ commit, dispatch }, userId) {
        commit('setUserStatus', 'Loading')
        axios
            .get("/api/users/" + userId)
            .then((res) => {
                commit('setUser', res.data)
                commit('setUserStatus', 'Success')
            })
            .catch((error) => {
                commit('setUserStatus', 'Error')
            })

    },
    







    sendFriendRequest({ commit, getters }, friendId) {
       
        if(getters.FriendButtontext !== 'Add Friend'){
           return;
        }

        axios.post('/api/friend-request', { 'friend_id': friendId })
            .then((res) => {
                commit('setUserFriendship', res.data)
            })
            .catch((error) => {

            })
    },
    acceptFriendRequest({ commit, state }, userID) {
        axios.post('/api/friend-request-response', { 'user_id': userID, 'status': 1 })
            .then((res) => {
                commit('setUserFriendship', res.data)
            })
            .catch((error) => {

            })
    },

    ignoreFriendRequest({ commit, state }, userID) {
        axios.delete('/api/friend-request-response/delete', { data: { 'user_id': userID } })
            .then((res) => {
                commit('setUserFriendship', null)
            })
            .catch((error) => {

            })
    }


};

const mutations = {
    setUser(state, user) {
        state.user = user;
    },
    setUserStatus(state, status) {
        state.userStatus = status;
    },
    setUserFriendship(state, friendship) {
        state.user.data.attributes.friendship = friendship;
    },
    

};

export default {
    state, getters, actions, mutations,
}

