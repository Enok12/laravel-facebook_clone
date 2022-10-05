<template>
  <div class="flex flex-col items-center">
    <div class="relative mb-8">
      <div class="w-100 h-64 overflow-hidden z-10">
        <img
          src="https://wac-cdn.atlassian.com/dam/jcr:ba03a215-2f45-40f5-8540-b2015223c918/Max-R_Headshot%20(1).jpg?cdnVersion=559"
          alt=""
          class="object-cover w-full"
        />
      </div>

      <div class="flex items-center absolute bottom-0 left-0 -mb-8 z-20 ml-12">
        <div class="w-32">
          <img
            src="https://www.diethelmtravel.com/wp-content/uploads/2016/04/bill-gates-wealthiest-person.jpg"
            alt=""
            class="object-cover w-32 h-32 border-4 border-gray-200 rounded-full"
          />
        </div>
        <p class="ml-4 text-2xl text-gray-100">
          {{ user.data.attributes.name }}
        </p>
      </div>
    </div>

    <p v-if="postLoading">Loading Posts......</p>

    <Post
      v-else
      v-for="post in posts.data"
      :key="post.data.post_id"
      :post="post"
    />

    <p v-if="!postLoading && posts.data.length < 1">Get Stareted</p>
  </div>
</template>

<script>
import Post from "../Post.vue";
export default {
  name: "Show",

  components: {
    Post,
  },
  data: () => {
    return {
      user: null,
      posts: null,
      userLoading: true,
      postLoading: true,
    };
  },
  mounted() {
    axios
      .get("/api/users/" + this.$route.params.userId)
      .then((res) => {
        this.user = res.data;
        this.userLoading = false;
      })
      .catch((error) => {
        console.log("unable to fetch user from the Server");
        this.userLoading = false;
      })
      .finally(() => {
        this.userLoading = false;
      });

    axios
      .get("/api/users/" + this.$route.params.userId + "/posts")
      .then((res) => {
        this.posts = res.data;
        this.postLoading = false;
      })
      .catch((error) => {
        console.log("Unable to catch posts");
        this.postLoading = false;
      })
      .finally(() => {
        this.postLoading = false;
      });
  },
};
</script>

<style scoped>
</style>