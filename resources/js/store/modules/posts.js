import { data } from "autoprefixer";

const state = {
    posts: null,
    postsStatus: null,
    postMessage:'',
};

const getters = {
    posts: state => {
        return state.posts
    },
    newsStatus: state => {
        return {
            postsStatus: state.postsStatus
        }
    },
    postMessage: state => {
        return state.postMessage
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
    },

    postMessage({ commit, state }) {

        commit('setPostsStatus', 'Loading')
        axios
            .post("/api/posts",{body:state.postMessage})
            .then((res) => {
                commit('pushPost', res.data)
                commit('updateMessage', '')

            })
            .catch((error) => {
               
            });
    },

    likePost({ commit, state },data) {
        console.log(data.postId);
        axios
        .post('/api/posts/'+data.postId+'/like')
        .then((res) => {
            commit('pushLikes', {likes:res.data,postKey:data.postKey})

        })
        .catch((error) => {
           
        });
    },
    fetchUserPosts({ commit, dispatch }, userId) {
        commit('SetpostsStatus', 'Loading')
        axios
            .get("/api/users/" + userId + "/posts")
            .then((res) => {
                commit('setPosts', res.data)
                commit('SetpostsStatus', 'Success')
            })
            .catch((error) => {
                commit('SetpostsStatus', 'Error')
            });

    },
    commentPost({ commit, state },data) {
        console.log(data.postId);
        axios
        .post('/api/posts/'+data.postId+'/comment',{body:data.body})
        .then((res) => {
            commit('pushComments', {comments:res.data,postKey:data.postKey})

        })
        .catch((error) => {
           
        });
    }
};

const mutations = {
    setPosts(state, posts) {
        state.posts = posts;
    },
    setPostsStatus(state, status) {
        state.postsStatus = status;
    },
    updateMessage(state, message) {
        state.postMessage = message;
    },
    pushPost(state, post) {
        state.posts.data.unshift(post);
    },
    pushLikes(state, data) {
        state.posts.data[data.postKey].data.attributes.likes = data.likes;
    },
    pushComments(state, data) {
        state.posts.data[data.postKey].data.attributes.comments = data.comments;
    }


};

export default {
    state, getters, actions, mutations,
}

