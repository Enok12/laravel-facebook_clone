<template>
  <div class="flex flex-col items-center py-4">
    <NewPost />
    <p v-if ="newsStatus.postStatus === 'Loading'">Loading Posts......</p>
    <Post v-else v-for="(post,postKey) in posts.data" :key="postKey" :post="post" />
  </div>
</template>

<script>
import NewPost from "../NewPost.vue";
import Post from "../Post.vue";
import {mapGetters} from 'vuex'

export default {
  name: "NewsFeed",

  components: {
    NewPost,
    Post,
  },

  mounted() {
   this.$store.dispatch('fetchNewsPosts');
  },
  computed:{
    ...mapGetters({
      posts : 'posts',
      newsStatus : 'newsStatus'
    })
  }
};
</script>

<style scoped>
</style>
