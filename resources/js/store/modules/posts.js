const state = {
    newsPosts: null,
    newsPostsStatus: null,
};

const getters = {
    newsPosts: state => {
        return state.newsPosts
    },
    newsStatus: state => {
        return {
            newsPostsStatus: state.newsPostsStatus
        }
    }
};

const actions = {
    fetchNewsPosts({ commit, state }) {

        commit('setPostsStatus', 'Loading')
        axios
            .get("/api/posts")
            .then((res) => {
                commit('setPosts', res.data)
                commit('setPostsStatus', 'Success')

            })
            .catch((error) => {
                commit('setPostsStatus', 'Error')


            });
    }
};

const mutations = {
    setPosts(state, posts) {
        state.newsPosts = posts;
    },
    setPostsStatus(state, status) {
        state.newsPostsStatus = status;
    }
};

export default {
    state, getters, actions, mutations,
}

