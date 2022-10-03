<template>
  <div class="flex flex-col items-center py-4">
    <NewPost />
    <Post v-for="post in posts.data" :key="post.data.post_id" :post="post" />
  </div>
</template>

<script>
import NewPost from "../NewPost.vue";
import Post from "../Post.vue";

export default {
  name: "NewsFeed",

  components: {
    NewPost,
    Post,
  },

  data: () => {
    return {
       posts:null 
    };
  },

  mounted() {
    axios
      .get("/api/posts")
      .then((res) => {
        this.posts = res.data;
      })
      .catch((error) => {
        console.log("Unable to catch posts");
      });
  },
};
</script>

<style scoped>
</style>
